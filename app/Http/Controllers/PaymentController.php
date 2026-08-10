<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\PaymentRequest;
use App\Services\MpesaService;
use App\Support\PhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $loans = Loan::query()
            ->with([
                'customer:id,name,phone',
                'loanProduct:id,name',
                'installments' => fn ($query) => $query
                    ->whereIn('status', ['pending', 'partial', 'overdue'])
                    ->orderBy('installment_number'),
            ])
            ->whereIn('status', ['disbursed', 'active'])
            ->whereHas('installments', function ($query) {
                $query->whereIn('status', [
                    'pending',
                    'partial',
                    'overdue',
                ]);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Repayments/Index', [
            'loans' => $loans,
        ]);
    }

    public function store(
        Request $request,
        Loan $loan,
        MpesaService $mpesa
    ): RedirectResponse {
        if (! in_array($loan->status, ['disbursed', 'active'], true)) {
            return back()->withErrors([
                'payment' => 'This loan is not available for repayment.',
            ]);
        }

        $validated = $request->validate([
            'loan_installment_id' => [
                'required',
                'integer',
                'exists:loan_installments,id',
            ],
        ]);

        $installment = LoanInstallment::where('id', $validated['loan_installment_id'])
            ->where('loan_id', $loan->id)
            ->first();

        if (! $installment) {
            return back()->withErrors([
                'payment' => 'The selected installment does not belong to this loan.',
            ]);
        }

        $outstanding = round(
            (float) $installment->amount_due
                - (float) $installment->amount_paid,
            2
        );

        if ($outstanding <= 0) {
            return back()->withErrors([
                'payment' => 'This installment has already been fully paid.',
            ]);
        }

        /*
         * Don't create another active request for the same installment.
         */
        $existingRequest = PaymentRequest::where(
            'loan_installment_id',
            $installment->id
        )
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($existingRequest) {
            return back()->withErrors([
                'payment' => 'A payment request is already pending for this installment.',
            ]);
        }

        $reference = 'PAY-INST-' . $installment->id . '-' . now()->format('YmdHis');

        $paymentRequest = PaymentRequest::create([
            'loan_id' => $loan->id,
            'customer_id' => $loan->customer_id,
            'loan_installment_id' => $installment->id,
            'amount' => $outstanding,
            'phone' => PhoneNumber::normalize(
                $loan->customer->phone
            ),
            'reference' => $reference,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            $response = $mpesa->stkPush(
                phone: $paymentRequest->phone,
                amount: $outstanding,
                reference: $reference,
                description: 'Loan #' . $loan->id . ' repayment'
            );

            $paymentRequest->update([
                'checkout_request_id' =>
                    $response['CheckoutRequestID'] ?? null,

                'merchant_request_id' =>
                    $response['MerchantRequestID'] ?? null,
            ]);

            return back()->with(
                'success',
                'Payment request sent to the customer.'
            );
        } catch (\Throwable $e) {
            $paymentRequest->update([
                'status' => 'failed',
            ]);

            report($e);

            return back()->withErrors([
                'payment' => 'Unable to initiate M-Pesa payment.',
            ]);
        }
    }
}
