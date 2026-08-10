<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Services\MpesaService;
use App\Support\PhoneNumber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DisburseLoanJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $loanId,
        public int $userId,
        public string $originatorConversationId
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MpesaService $mpesa): void
    {
        $loan = Loan::with('customer')->findOrFail($this->loanId);

        $transaction = $loan->transactions()
            ->where('type', 'disbursement')
            ->where('reference', $this->originatorConversationId)
            ->firstOrFail();

        if (data_get($transaction->metadata, 'status') !== 'pending') {
            return;
        }

        /*
         * DEVELOPMENT MODE
         *
         * Simulate a successful B2C response without contacting
         * Safaricom Daraja.
         */
        if (config('mpesa.disburse_environment') === 'development') {
            $this->simulateDisbursement($loan, $transaction);

            Log::info('After simulation in job', [
                'loan_id' => $loan->id,
                'status' => $loan->fresh()->status,
            ]);

            return;
        }


        $phone = PhoneNumber::normalize(
            $loan->customer->phone
        );

        $response = $mpesa->b2c(
            phone: $phone,
            amount: (float) $loan->principal,
            originatorConversationId: $this->originatorConversationId,
            remarks: 'Loan #' . $loan->id . ' disbursement'
        );

        $transaction->update([
            'metadata' => [
                ...($transaction->metadata ?? []),
                'status' => 'submitted',
                'source' => 'mpesa_b2c',
                'originator_conversation_id' =>
                    $this->originatorConversationId,
                'conversation_id' =>
                    $response['ConversationID'] ?? null,
                'response' => $response,
            ],
        ]);
    }

    private function simulateDisbursement(
        Loan $loan,
        LoanTransaction $transaction
    ): void {
        DB::transaction(function () use ($loan, $transaction) {
            $simulatedReceipt =
                'SIM-B2C-' . $loan->id . '-' . now()->format('YmdHis');

            $transaction->update([
                'metadata' => [
                    ...($transaction->metadata ?? []),
                    'status' => 'completed',
                    'source' => 'simulated_b2c',
                    'simulated' => true,
                    'originator_conversation_id' =>
                        $this->originatorConversationId,
                    'conversation_id' =>
                        'SIM-CONV-' . $loan->id,
                    'transaction_receipt' =>
                        $simulatedReceipt,
                    'result_code' => 0,
                    'result_description' =>
                        'Simulated B2C disbursement successful.',
                ],
            ]);

            $loan->status = 'disbursed';
            $loan->save();
        });

        Log::info('Simulated disbursement completed', [
            'loan_id' => $loan->id,
            'status' => $loan->fresh()->status,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $loan = Loan::find($this->loanId);

        if (! $loan) {
            return;
        }

        $transaction = $loan->transactions()
            ->where('type', 'disbursement')
            ->where('reference', $this->originatorConversationId)
            ->first();

        if (! $transaction) {
            return;
        }

        $transaction->update([
            'metadata' => [
                ...($transaction->metadata ?? []),
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ],
        ]);

        /*
         * Since no successful B2C confirmation exists,
         * the loan remains approved.
         */
        $loan->installments()->delete();

        $loan->status = 'approved';
        $loan->disbursed_at = null;
        $loan->disbursed_by = null;
        $loan->save();
    }
}
