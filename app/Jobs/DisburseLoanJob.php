<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Services\MpesaService;
use App\Support\PhoneNumber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
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

        $loan->update([
            'status' => 'approved',
            'disbursed_at' => null,
            'disbursed_by' => null,
        ]);
    }
}
