<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\DB;

class PaymentAllocationService
{
    public function allocate(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $loan = Loan::with('installments')
                ->lockForUpdate()
                ->findOrFail($payment->loan_id);

            $remaining = (float) $payment->amount;

            $installments = $loan->installments()
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->orderBy('installment_number')
                ->lockForUpdate()
                ->get();

            foreach ($installments as $installment) {
                if ($remaining <= 0) {
                    break;
                }

                $remaining = $this->allocateToInstallment(
                    $payment,
                    $installment,
                    $remaining,
                    $loan->repayment_order
                );
            }

            if ($remaining > 0) {
                throw new \RuntimeException(
                    'Payment exceeds the outstanding loan balance.'
                );
            }
        });
    }

    private function allocateToInstallment(
        Payment $payment,
        LoanInstallment $installment,
        float $remaining,
        string $repaymentOrder
    ): float {
        $components = $repaymentOrder === 'interest_first'
            ? ['interest', 'principal', 'fee']
            : ['principal', 'interest', 'fee'];

        foreach ($components as $component) {
            if ($remaining <= 0) {
                break;
            }

            $due = match ($component) {
                'principal' => (float) $installment->principal_due,
                'interest' => (float) $installment->interest_due,
                'fee' => (float) $installment->fee_due,
            };

            $alreadyPaid = (float) $installment->paymentAllocations()
                ->where('component', $component)
                ->sum('amount');

            $outstanding = max(0, $due - $alreadyPaid);

            if ($outstanding <= 0) {
                continue;
            }

            $allocation = min($remaining, $outstanding);

            $installment->paymentAllocations()->create([
                'payment_id' => $payment->id,
                'component' => $component,
                'amount' => $allocation,
            ]);

            $remaining -= $allocation;
        }

        $this->refreshInstallmentStatus($installment);

        return $remaining;
    }

    private function refreshInstallmentStatus(
        LoanInstallment $installment
    ): void {
        $principalPaid = (float) $installment->paymentAllocations()
            ->where('component', 'principal')
            ->sum('amount');

        $interestPaid = (float) $installment->paymentAllocations()
            ->where('component', 'interest')
            ->sum('amount');

        $feePaid = (float) $installment->paymentAllocations()
            ->where('component', 'fee')
            ->sum('amount');

        $totalPaid = $principalPaid + $interestPaid + $feePaid;

        $installment->update([
            'amount_paid' => $totalPaid,
            'status' => $totalPaid >= (float) $installment->amount_due
                ? 'paid'
                : ($totalPaid > 0 ? 'partial' : $installment->status),
            'paid_at' => $totalPaid >= (float) $installment->amount_due
                ? now()
                : null,
        ]);
    }
}
