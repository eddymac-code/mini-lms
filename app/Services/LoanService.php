<?php

namespace App\Services;

use App\Jobs\DisburseLoanJob;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LoanService
{
    public function __construct(
        private LoanScheduleService $scheduleService,
    ) {
    }

    /**
     * Create a loan application.
     *
     * Financial configuration is copied from the product so that
     * subsequent product changes cannot alter an existing loan.
     */
    public function create(array $data): Loan
    {
        $product = LoanProduct::query()
            ->whereKey($data['loan_product_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $principal = (float) $data['principal'];

        if (
            $principal < (float) $product->min_principal ||
            $principal > (float) $product->max_principal
        ) {
            throw new InvalidArgumentException(
                'The principal is outside the allowed range for this loan product.'
            );
        }

        return Loan::create([
            'customer_id' => $data['customer_id'],
            'loan_product_id' => $product->id,

            // Actual requested loan amount.
            'principal' => $principal,

            // Snapshot product configuration onto the loan.
            'interest_rate' => $product->interest_rate,
            'interest_method' => $product->interest_method,
            'term' => $product->term,
            'term_unit' => $product->term_unit,
            'repayment_order' => $product->repayment_order,

            'status' => 'pending',
        ]);
    }

    /**
     * Approve a pending loan.
     */
    public function approve(
        Loan $loan,
        User $user,
        ?Carbon $approvedAt = null
    ): Loan {
        if ($loan->status !== 'pending') {
            throw new InvalidArgumentException(
                'Only pending loans can be approved.'
            );
        }

        $approvedAt ??= now();

        $loan->approved_by = $user->id;
        $loan->approved_at = $approvedAt;
        $loan->status = 'approved';
        $loan->save();

        return $loan->refresh();
    }

    /**
     * Disburse an approved loan and generate its schedule.
     *
     * Daraja B2C initiation launched here.
     */
    public function disburse(
        Loan $loan,
        User $user,
        ?Carbon $disbursedAt = null
    ): Loan {
        if ($loan->status !== 'approved') {
            throw new InvalidArgumentException(
                'Only approved loans can be disbursed.'
            );
        }

        if ($loan->installments()->exists()) {
            throw new InvalidArgumentException(
                'This loan already has a repayment schedule.'
            );
        }

        $disbursedAt ??= now();

        $originatorConversationId = 'LOAN-DISB-' . $loan->id . '-' . now()->format('YmdHis');

        return DB::transaction(function () use (
            $loan,
            $user,
            $disbursedAt,
            $originatorConversationId
        ) {

            $loan->disbursed_by = $user->id;
            $loan->disbursed_at = $disbursedAt;
            $loan->save();

            $loan->refresh();

            // Generate the repayment schedule from the disbursement date.
            $this->scheduleService->generate($loan);

            // Record the financial disbursement transaction.
            $loan->transactions()->create([
                'type' => 'disbursement',
                'amount' => $loan->principal,
                'transaction_date' => $disbursedAt,
                'reference' => $originatorConversationId,
                'payment_id' => null,
                'created_by' => $user->id,
                'metadata' => [
                    'status' => 'pending',
                    'source' => 'mpesa_b2c',
                    'originator_conversation_id' => $originatorConversationId,
                    'interest_method' => $loan->interest_method,
                    'interest_rate' => $loan->interest_rate,
                    'term' => $loan->term,
                    'term_unit' => $loan->term_unit,
                ],
            ]);

            /*
            * Only dispatch after the DB transaction commits.
            */
            DisburseLoanJob::dispatch(
                $loan->id,
                $user->id,
                $originatorConversationId
            )->afterCommit();

            return $loan->refresh();
        });
    }
}
