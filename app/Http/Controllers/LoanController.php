<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\LoanProduct;
use App\Models\PaymentRequest;
use App\Services\LoanService;
use App\Services\MpesaService;
use App\Support\PhoneNumber;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class LoanController extends Controller
{
    public function __construct(
        private LoanService $loanService,
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('Loans/Index', [
            'loans' => Loan::query()
                ->with([
                    'customer',
                    'loanProduct',
                ])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Loans/Create', [
            'customers' => Customer::query()
                ->orderBy('name', 'asc')
                ->get([
                    'id',
                    'name',
                    'phone',
                ]),

            'loanProducts' => LoanProduct::query()
                ->where('is_active', true)
                ->orderBy('name', 'asc')
                ->get(),
        ]);
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        try {
            $loan = $this->loanService->create(
                $request->validated()
            );

            return to_route('loans.show', $loan)
                ->with(
                    'success',
                    'Loan application created successfully.'
                );
        } catch (InvalidArgumentException $e) {
            return back()
                ->withErrors([
                    'principal' => $e->getMessage(),
                ])
                ->withInput();
        }
    }

    public function show(Loan $loan): Response
    {
        $loan->load([
            'customer',
            'loanProduct',
            'approvedBy',
            'disbursedBy',
            'installments',
            'transactions',
        ]);

        return Inertia::render('Loans/Show', [
            'loan' => $loan,
        ]);
    }

    public function approve(
        Request $request,
        Loan $loan
    ): RedirectResponse {

        try {
            $approvedAt = $request->filled('approved_at')
                ? Carbon::parse($request->input('approved_at'))
                : now();

            $this->loanService->approve(
                $loan,
                $request->user(),
                $approvedAt
            );

            return back()->with(
                'success',
                'Loan approved successfully.'
            );
        } catch (InvalidArgumentException $e) {
            return back()->withErrors([
                'loan' => $e->getMessage(),
            ]);
        }
    }

    public function disburse(
        Request $request,
        Loan $loan
    ): RedirectResponse {
        try {
            $disbursedAt = $request->filled('disbursed_at')
                ? Carbon::parse($request->input('disbursed_at'))
                : now();

            $this->loanService->disburse(
                $loan,
                $request->user(),
                $disbursedAt
            );

            return back()->with(
                'success',
                'Loan disbursement has been submitted for processing.'
            );
        } catch (InvalidArgumentException $e) {
            return back()->withErrors([
                'loan' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors([
                'loan' => 'Unable to initiate loan disbursement.',
            ]);
        }
    }

    public function requestRepayment(
        Loan $loan,
        LoanInstallment $installment,
        MpesaService $mpesa
    ): RedirectResponse {
        abort_unless(
            $installment->loan_id === $loan->id,
            404
        );

        abort_unless(
            in_array($loan->status, ['disbursed', 'active'], true),
            422,
            'This loan is not available for repayment.'
        );

        $outstanding = round(
            (float) $installment->amount_due
            - (float) $installment->amount_paid,
            2
        );

        if ($outstanding <= 0) {
            return back()->with('error', 'This installment is already paid.');
        }

        $existingRequest = PaymentRequest::where('loan_id', $loan->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingRequest) {
            return back()->with(
                'error',
                'There is already a pending M-Pesa payment request for this loan.'
            );
        }

        $phone = PhoneNumber::normalize($loan->customer->phone);

        $reference = 'INST-' . $installment->id . '-' . Str::random(8);

        $paymentRequest = PaymentRequest::create([
            'loan_id' => $loan->id,
            'customer_id' => $loan->customer_id,
            'amount' => $outstanding,
            'phone' => $phone,
            'reference' => $reference,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            $response = $mpesa->stkPush(
                phone: $phone,
                amount: $outstanding,
                reference: $reference,
                description: 'Loan repayment'
            );

            $paymentRequest->update([
                'checkout_request_id' => $response['CheckoutRequestID'] ?? null,
                'merchant_request_id' => $response['MerchantRequestID'] ?? null,
            ]);
        } catch (\Throwable $e) {
            $paymentRequest->update([
                'status' => 'failed',
            ]);

            report($e);

            return back()->with(
                'error',
                'Unable to initiate the M-Pesa payment.'
            );
        }

        return back()->with(
            'success',
            'M-Pesa payment prompt sent to ' . $phone . '.'
        );
    }
}
