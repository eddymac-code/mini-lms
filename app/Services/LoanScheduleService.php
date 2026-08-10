<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanInstallment;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LoanScheduleService
{
    /**
     * Generate the repayment schedule for a disbursed loan.
     *
     * The schedule is anchored to the loan's disbursement date.
     */
    public function generate(Loan $loan): Collection
    {
        if (! $loan->disbursed_at) {
            throw new InvalidArgumentException(
                'A loan must have a disbursement date before its schedule can be generated.'
            );
        }

        if ($loan->installments()->exists()) {
            throw new InvalidArgumentException(
                'This loan already has a repayment schedule.'
            );
        }

        return DB::transaction(function () use ($loan) {
            return match ($loan->interest_method) {
                'flat' => $this->generateFlatSchedule($loan),
                'declining_balance_equal_installment' =>
                    $this->generateDecliningBalanceSchedule($loan),
                default => throw new InvalidArgumentException(
                    "Unsupported interest method: {$loan->interest_method}"
                ),
            };
        });
    }

    /**
     * Flat-rate schedule.
     */
    private function generateFlatSchedule(Loan $loan): Collection
    {
        $installmentCount = $this->installmentCount($loan);

        $totalInterest = round(
            (float) $loan->principal * ((float) $loan->interest_rate / 100),
            2
        );

        $totalRepayment = round(
            (float) $loan->principal + $totalInterest,
            2
        );

        $principalParts = $this->splitAmount(
            (float) $loan->principal,
            $installmentCount
        );

        $interestParts = $this->splitAmount(
            $totalInterest,
            $installmentCount
        );

        return $this->persistSchedule(
            $loan,
            $principalParts,
            $interestParts
        );
    }

    /**
     * Declining-balance equal-installment amortization.
     *
     * Each installment is approximately equal, but the interest portion
     * decreases as the outstanding principal decreases.
     */
    private function generateDecliningBalanceScheduleFirst(Loan $loan): Collection
    {
        $installmentCount = $this->installmentCount($loan);

        $principal = (float) $loan->principal;

        /*
         * Interest rate is stored as an annual percentage.
         *
         * Convert it to the appropriate periodic rate:
         *
         * weekly  = annual rate / 52
         * monthly = annual rate / 12
         */
        $periodicRate = $this->periodicInterestRate($loan);

        /*
         * Zero-interest loans are valid and need a separate calculation
         * because the standard amortization formula divides by zero.
         */
        if ($periodicRate == 0.0) {
            $principalParts = $this->splitAmount(
                $principal,
                $installmentCount
            );

            $interestParts = array_fill(
                0,
                $installmentCount,
                0.00
            );

            return $this->persistSchedule(
                $loan,
                $principalParts,
                $interestParts
            );
        }

        /*
         * Standard amortization formula:
         *
         * A = P * [r(1+r)^n] / [(1+r)^n - 1]
         */
        $factor = pow(1 + $periodicRate, $installmentCount);

        $installmentAmount = $principal *
            (
                ($periodicRate * $factor) /
                ($factor - 1)
            );

        $installmentAmount = round($installmentAmount, 2);

        $principalParts = [];
        $interestParts = [];

        $remainingPrincipal = $principal;

        for ($i = 1; $i <= $installmentCount; $i++) {
            /*
             * Interest is calculated against the opening balance.
             */
            $interest = round(
                $remainingPrincipal * $periodicRate,
                2
            );

            /*
             * Normally the installment is fixed.
             * The final installment is adjusted for rounding.
             */
            if ($i === $installmentCount) {
                $principalPayment = round(
                    $remainingPrincipal,
                    2
                );

                $interest = round(
                    $remainingPrincipal * $periodicRate,
                    2
                );
            } else {
                $principalPayment = round(
                    $installmentAmount - $interest,
                    2
                );

                /*
                 * Protect against rounding causing the final balance
                 * to become negative.
                 */
                $principalPayment = min(
                    $principalPayment,
                    round($remainingPrincipal, 2)
                );
            }

            $principalParts[] = $principalPayment;
            $interestParts[] = $interest;

            $remainingPrincipal = round(
                $remainingPrincipal - $principalPayment,
                2
            );
        }

        return $this->persistSchedule(
            $loan,
            $principalParts,
            $interestParts
        );
    }

    private function generateDecliningBalanceSchedule(Loan $loan): Collection
    {
        $principal = round((float) $loan->principal, 2);
        $rate = ((float) $loan->interest_rate) / 100;
        $periods = $loan->term;
        $installments = collect();

        if ($periods <= 0) {
            throw new \InvalidArgumentException('Loan term must be greater than zero.');
        }

        if ($rate < 0) {
            throw new \InvalidArgumentException('Interest rate cannot be negative.');
        }

        /*
        * Interest rate is per repayment period.
        *
        * 1% weekly  => 0.01 per week
        * 1% monthly => 0.01 per month
        */
        if ($rate == 0.0) {
            $regularPayment = round($principal / $periods, 2);
        } else {
            $regularPayment = round(
                $principal
                * ($rate * pow(1 + $rate, $periods))
                / (pow(1 + $rate, $periods) - 1),
                2
            );
        }

        $remainingPrincipal = $principal;

        for ($i = 1; $i <= $periods; $i++) {
            $interestDue = round(
                $remainingPrincipal * $rate,
                2
            );

            $feeDue = $loan->loanProduct->fee_is_recurring
                ? (float) $loan->loanProduct->fees
                : ($i === 1 ? (float) $loan->loanProduct->fees : 0);

            /*
            * Make the final installment absorb rounding differences
            * so the principal balance ends exactly at zero.
            */
            if ($i === $periods) {
                $principalDue = round($remainingPrincipal, 2);

                /*
                * The final installment consists of:
                *
                * remaining principal
                * + final period interest
                * + applicable fee
                */
                $amountDue = round(
                    $principalDue + $interestDue + $feeDue,
                    2
                );
            } else {
                /*
                * regularPayment contains only principal + interest.
                * Fees are added separately.
                */
                $principalDue = round(
                    $regularPayment - $interestDue,
                    2
                );

                $amountDue = round(
                    $regularPayment + $feeDue,
                    2
                );
            }

            $remainingPrincipal = round(
                $remainingPrincipal - $principalDue,
                2
            );

            $dueDate = $this->calculateDueDate(
                $loan->disbursed_at,
                $i,
                $loan->term_unit
            );

            $installment = LoanInstallment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $dueDate,
                'principal_due' => $principalDue,
                'interest_due' => $interestDue,
                'fee_due' => $feeDue,
                'amount_due' => $amountDue,
                'amount_paid' => 0,
                'status' => 'pending',
            ]);

            $installments->push($installment);
        }

        return $installments;
    }

    private function calculateDueDate(
        CarbonImmutable $disbursedAt,
        int $period,
        string $termUnit
    ): CarbonImmutable {
        return match ($termUnit) {
            'weeks' => $disbursedAt->copy()->addWeeks($period),
            'months' => $disbursedAt->copy()->addMonths($period),
            default => throw new \InvalidArgumentException(
                "Unsupported term unit: {$termUnit}"
            ),
        };
    }

    /**
     * Persist the calculated schedule.
     */
    private function persistSchedule(
        Loan $loan,
        array $principalParts,
        array $interestParts
    ): Collection {
        $installments = collect();

        $count = count($principalParts);

        for ($i = 0; $i < $count; $i++) {
            $principal = round($principalParts[$i], 2);
            $interest = round($interestParts[$i], 2);

            $installments->push(
                $loan->installments()->create([
                    'installment_number' => $i + 1,
                    'due_date' => $this->dueDate($loan, $i + 1),
                    'principal_due' => $principal,
                    'interest_due' => $interest,
                    'amount_due' => round(
                        $principal + $interest,
                        2
                    ),
                    'amount_paid' => 0,
                    'status' => 'pending',
                ])
            );
        }

        return $installments;
    }

    /**
     * Determine number of installments.
     *
     * For this assessment:
     * weeks  -> weekly installments
     * months -> monthly installments
     */
    private function installmentCount(Loan $loan): int
    {
        return $loan->term;
    }

    /**
     * Convert annual interest rate to periodic rate.
     */
    private function periodicInterestRate(Loan $loan): float
    {
        $annualRate = (float) $loan->interest_rate / 100;

        return match ($loan->term_unit) {
            'weeks' => $annualRate / 52,
            'months' => $annualRate / 12,
            default => throw new InvalidArgumentException(
                "Unsupported term unit: {$loan->term_unit}"
            ),
        };
    }

    /**
     * Calculate an installment due date from disbursement.
     */
    private function dueDate(Loan $loan, int $installmentNumber): CarbonImmutable
    {
        $date = $loan->disbursed_at->copy();

        return match ($loan->term_unit) {
            'weeks' => $date->addWeeks($installmentNumber),
            'months' => $date->addMonthsNoOverflow($installmentNumber),
            default => throw new InvalidArgumentException(
                "Unsupported term unit: {$loan->term_unit}"
            ),
        };
    }

    /**
     * Split an amount across installments while preserving the exact total.
     *
     * Example:
     * 100 / 3 = 33.33, 33.33, 33.34
     */
    private function splitAmount(float $amount, int $parts): array
    {
        $amount = round($amount, 2);

        $base = floor(($amount / $parts) * 100) / 100;

        $values = array_fill(
            0,
            $parts,
            round($base, 2)
        );

        $distributed = round(
            $base * $parts,
            2
        );

        $remainder = round(
            $amount - $distributed,
            2
        );

        $values[$parts - 1] = round(
            $values[$parts - 1] + $remainder,
            2
        );

        return $values;
    }
}
