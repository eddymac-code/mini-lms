<?php

namespace App\Http\Controllers;

use App\Models\DarajaCallback;
use App\Models\LoanInstallment;
use App\Models\LoanTransaction;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\PaymentRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MpesaCallbackController extends Controller
{
    public function stk(): JsonResponse
    {
        $payload = request()->all();

        $callback = $payload['Body']['stkCallback'] ?? [];

        $checkoutRequestId = $callback['CheckoutRequestID'] ?? null;
        $merchantRequestId = $callback['MerchantRequestID'] ?? null;
        $resultCode = $callback['ResultCode'] ?? null;

        $darajaCallback = DarajaCallback::create([
            'type' => 'stk',
            'payload' => $payload,
            'checkout_request_id' => $checkoutRequestId,
            'merchant_request_id' => $merchantRequestId,
            'mpesa_receipt_number' => $this->callbackValue(
                $callback['CallbackMetadata']['Item'] ?? [],
                'MpesaReceiptNumber'
            ),
            'received_at' => now(),
        ]);

        $paymentRequest = PaymentRequest::where(
            'checkout_request_id',
            $checkoutRequestId
        )->first();

        if (! $paymentRequest) {
            $darajaCallback->update([
                'processed_at' => now(),
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted',
            ]);
        }

        if ($paymentRequest->status !== 'pending') {
            $darajaCallback->update([
                'processed_at' => now(),
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Already processed',
            ]);
        }

        if ((int) $resultCode !== 0) {
            $paymentRequest->update([
                'status' => 'failed',
            ]);

            $darajaCallback->update([
                'processed_at' => now(),
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted',
            ]);
        }

        DB::transaction(function () use (
            $paymentRequest,
            $callback,
            $darajaCallback
        ) {
            $items = $callback['CallbackMetadata']['Item'] ?? [];

            $receipt = $this->callbackValue(
                $items,
                'MpesaReceiptNumber'
            );

            $amount = (float) $this->callbackValue(
                $items,
                'Amount'
            );

            $transactionDate = $this->callbackValue(
                $items,
                'TransactionDate'
            );


            // Idempotency protection.

            if (Payment::where(
                'mpesa_receipt_number',
                $receipt
            )->exists()) {
                $paymentRequest->update([
                    'status' => 'completed',
                ]);

                $darajaCallback->update([
                    'processed_at' => now(),
                ]);

                return;
            }

            $payment = Payment::create([
                'loan_id' => $paymentRequest->loan_id,
                'customer_id' => $paymentRequest->customer_id,
                'payment_request_id' => $paymentRequest->id,
                'amount' => $amount,
                'phone' => $paymentRequest->phone,
                'mpesa_receipt_number' => $receipt,
                'transaction_date' => $this->parseTransactionDate(
                    $transactionDate
                ),
            ]);

            $installment = LoanInstallment::lockForUpdate()
                ->findOrFail($paymentRequest->loan_installment_id);

            $outstanding = round(
                (float) $installment->amount_due
                - (float) $installment->amount_paid,
                2
            );

            if ($outstanding <= 0) {
                $paymentRequest->update([
                    'status' => 'completed',
                ]);

                $darajaCallback->update([
                    'processed_at' => now(),
                ]);

                return;
            }

            $allocation = min($amount, $outstanding);

            PaymentAllocation::create([
                'payment_id' => $payment->id,
                'loan_installment_id' => $installment->id,
                'amount' => $allocation,
            ]);

            $newAmountPaid = round(
                (float) $installment->amount_paid + $allocation,
                2
            );

            $installment->amount_paid = $newAmountPaid;

            if ($newAmountPaid >= (float) $installment->amount_due) {
                $installment->status = 'paid';
                $installment->paid_at = now();
            } else {
                $installment->status = 'partial';
            }

            $installment->save();

            LoanTransaction::create([
                'loan_id' => $paymentRequest->loan_id,
                'type' => 'repayment',
                'amount' => $amount,
                'transaction_date' => $payment->transaction_date,
                'reference' => $receipt,
                'payment_id' => $payment->id,
                'metadata' => [
                    'source' => 'mpesa_stk',
                    'checkout_request_id' => $paymentRequest->checkout_request_id,
                    'loan_installment_id' =>  $installment->id,
                    'allocated_amount' => $allocation,
                ],
            ]);

            $paymentRequest->update([
                'status' => 'completed',
            ]);

            $darajaCallback->update([
                'processed_at' => now(),
            ]);
        });

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    private function callbackValue(
        array $items,
        string $name
    ): mixed {
        foreach ($items as $item) {
            if (($item['Name'] ?? null) === $name) {
                return $item['Value'] ?? null;
            }
        }

        return null;
    }

    private function parseTransactionDate(
        mixed $value
    ): Carbon {
        return Carbon::createFromFormat(
            'YmdHis',
            (string) $value
        );
    }

    public function b2cResult(): JsonResponse
    {
        $payload = request()->all();

        $result = $payload['Result'] ?? [];

        $originatorConversationId =
            $result['OriginatorConversationID'] ?? null;

        $conversationId =
            $result['ConversationID'] ?? null;

        $resultCode = $result['ResultCode'] ?? null;

        $receipt = data_get(
            $result,
            'ResultParameters.ResultParameter',
        );

        $receiptNumber = $this->resultValue(
            $receipt,
            'TransactionReceipt'
        );

        $callback = DarajaCallback::create([
            'type' => 'b2c_result',
            'payload' => $payload,
            'mpesa_receipt_number' => $receiptNumber,
            'received_at' => now(),
        ]);

        $transaction = LoanTransaction::where(
            'reference',
            $originatorConversationId
        )->first();

        if (! $transaction) {
            $callback->update([
                'processed_at' => now(),
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted',
            ]);
        }

        /*
        * Idempotency.
        */
        if (
            data_get($transaction->metadata, 'status')
            === 'completed'
        ) {
            $callback->update([
                'processed_at' => now(),
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Already processed',
            ]);
        }

        DB::transaction(function () use (
            $transaction,
            $result,
            $receiptNumber,
            $callback
        ) {
            $metadata = $transaction->metadata ?? [];

            $metadata['status'] =
                ((int) $result['ResultCode'] === 0)
                    ? 'completed'
                    : 'failed';

            $metadata['result_code'] =
                $result['ResultCode'] ?? null;

            $metadata['result_description'] =
                $result['ResultDesc'] ?? null;

            $metadata['conversation_id'] =
                $result['ConversationID'] ?? null;

            $metadata['transaction_receipt'] =
                $receiptNumber;

            $transaction->metadata = $metadata;

            $transaction->save();

            if ((int) ($result['ResultCode'] ?? -1) === 0) {
                $transaction->loan->update([
                    'status' => 'disbursed',
                ]);
            } else {
                /*
                * The B2C request failed, so the loan remains approved.
                * The generated schedule is removed because no
                * disbursement occurred.
                */
                $transaction->loan->installments()->delete();

                $transaction->loan->update([
                    'status' => 'approved',
                    'disbursed_at' => null,
                    'disbursed_by' => null,
                ]);
            }

            $callback->update([
                'processed_at' => now(),
            ]);
        });

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function b2cTimeout(): JsonResponse
    {
        $payload = request()->all();

        DarajaCallback::create([
            'type' => 'b2c_timeout',
            'payload' => $payload,
            'received_at' => now(),
        ]);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    private function resultValue(
        ?array $parameters,
        string $name
    ): mixed {
        foreach ($parameters ?? [] as $parameter) {
            if (($parameter['Key'] ?? null) === $name) {
                return $parameter['Value'] ?? null;
            }
        }

        return null;
    }
}
