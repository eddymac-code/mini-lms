<?php

namespace App\Http\Controllers;

use App\Models\Loan;
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
        if (! in_array($loan->status, ['disbursed', 'active'])) {
            return back()->withErrors([
                'payment' => 'This loan is not available for repayment.',
            ]);
        }

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = (float) $request->amount;

        $reference = 'PAY-' . $loan->id . '-' . now()->format('YmdHis');

        $paymentRequest = PaymentRequest::create([
            'loan_id' => $loan->id,
            'customer_id' => $loan->customer_id,
            'amount' => $amount,
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
                amount: $amount,
                reference: $reference,
                description: 'Loan repayment'
            );

            $paymentRequest->update([
                'checkout_request_id' =>
                    $response['CheckoutRequestID'] ?? null,

                'merchant_request_id' =>
                    $response['MerchantRequestID'] ?? null,
            ]);

            return back()->with(
                'success',
                'M-Pesa payment request sent successfully.'
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
