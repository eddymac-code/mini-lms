<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $id_number
 * @property string|null $email
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Loan> $loans
 * @property-read int|null $loans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentRequest> $paymentRequests
 * @property-read int|null $payment_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property array<array-key, mixed> $payload
 * @property string|null $checkout_request_id
 * @property string|null $merchant_request_id
 * @property string|null $mpesa_receipt_number
 * @property \Carbon\CarbonImmutable $received_at
 * @property \Carbon\CarbonImmutable|null $processed_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereCheckoutRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereMerchantRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereMpesaReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DarajaCallback whereUpdatedAt($value)
 */
	class DarajaCallback extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property int $loan_product_id
 * @property numeric $principal
 * @property numeric $interest_rate
 * @property string $interest_method
 * @property int $term
 * @property string $term_unit
 * @property string $repayment_order
 * @property string $status
 * @property int|null $approved_by
 * @property \Carbon\CarbonImmutable|null $approved_at
 * @property int|null $disbursed_by
 * @property \Carbon\CarbonImmutable|null $disbursed_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\User|null $disbursedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanInstallment> $installments
 * @property-read int|null $installments_count
 * @property-read \App\Models\LoanProduct $loanProduct
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentRequest> $paymentRequests
 * @property-read int|null $payment_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereDisbursedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereDisbursedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereInterestMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereInterestRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan wherePrincipal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereRepaymentOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereTermUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereUpdatedAt($value)
 */
	class Loan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $loan_id
 * @property int $installment_number
 * @property \Carbon\CarbonImmutable $due_date
 * @property numeric $principal_due
 * @property numeric $interest_due
 * @property numeric $amount_due
 * @property numeric $amount_paid
 * @property string $status
 * @property \Carbon\CarbonImmutable|null $paid_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentAllocation> $paymentAllocations
 * @property-read int|null $payment_allocations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereAmountDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereInstallmentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereInterestDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment wherePrincipalDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanInstallment whereUpdatedAt($value)
 */
	class LoanInstallment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property numeric $min_principal
 * @property numeric $max_principal
 * @property numeric $default_principal
 * @property numeric $interest_rate
 * @property string $interest_method
 * @property int $term
 * @property string $term_unit
 * @property string $repayment_order
 * @property numeric $fees
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Loan> $loans
 * @property-read int|null $loans_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereDefaultPrincipal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereInterestMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereInterestRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereMaxPrincipal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereMinPrincipal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereRepaymentOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereTermUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanProduct whereUpdatedAt($value)
 */
	class LoanProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $loan_id
 * @property string $type
 * @property numeric $amount
 * @property \Carbon\CarbonImmutable $transaction_date
 * @property string|null $reference
 * @property int|null $payment_id
 * @property int|null $created_by
 * @property array<array-key, mixed>|null $metadata
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Loan $loan
 * @property-read \App\Models\Payment|null $payment
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereUpdatedAt($value)
 */
	class LoanTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $loan_id
 * @property int $customer_id
 * @property int|null $payment_request_id
 * @property numeric $amount
 * @property string $phone
 * @property string $mpesa_receipt_number
 * @property \Carbon\CarbonImmutable $transaction_date
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentAllocation> $allocations
 * @property-read int|null $allocations_count
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Loan $loan
 * @property-read \App\Models\PaymentRequest|null $paymentRequest
 * @property-read \App\Models\LoanTransaction|null $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMpesaReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $payment_id
 * @property int $loan_installment_id
 * @property numeric $amount
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\LoanInstallment $loanInstallment
 * @property-read \App\Models\Payment $payment
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation whereLoanInstallmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentAllocation whereUpdatedAt($value)
 */
	class PaymentAllocation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $loan_id
 * @property int $customer_id
 * @property numeric $amount
 * @property string $phone
 * @property string $reference
 * @property string $status
 * @property string|null $checkout_request_id
 * @property string|null $merchant_request_id
 * @property \Carbon\CarbonImmutable|null $expires_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Loan $loan
 * @property-read \App\Models\Payment|null $payment
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereCheckoutRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereMerchantRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentRequest whereUpdatedAt($value)
 */
	class PaymentRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $is_super_admin
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsSuperAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

