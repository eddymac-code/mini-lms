<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LoanProductController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('LoanProducts/Index', [
            'loanProducts' => LoanProduct::query()
                ->latest()
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('LoanProducts/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        LoanProduct::create($validated);

        return to_route('loan-products')
            ->with('success', 'Loan product created successfully.');
    }

    public function show(LoanProduct $loanProduct): Response
    {
        return Inertia::render('LoanProducts/Show', [
            'loanProduct' => $loanProduct,
        ]);
    }

    public function edit(LoanProduct $loanProduct): Response
    {
        return Inertia::render('LoanProducts/Edit', [
            'loanProduct' => $loanProduct,
        ]);
    }

    public function update(
        Request $request,
        LoanProduct $loanProduct
    ): RedirectResponse {
        $validated = $this->validateProduct($request);

        $loanProduct->update($validated);

        return to_route('loan-products')
            ->with('success', 'Loan product updated successfully.');
    }

    public function destroy(LoanProduct $loanProduct): RedirectResponse
    {
        if ($loanProduct->loans()->exists()) {
            return back()->withErrors([
                'loanProduct' => 'A loan product with existing loans cannot be deleted.',
            ]);
        }

        $loanProduct->delete();

        return to_route('loan-products')
            ->with('success', 'Loan product deleted successfully.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'min_principal' => ['required', 'numeric', 'min:0'],
            'max_principal' => ['required', 'numeric', 'gt:min_principal'],
            'default_principal' => [
                'required',
                'numeric',
                'gte:min_principal',
                'lte:max_principal',
            ],

            'interest_rate' => ['required', 'numeric', 'min:0'],
            'interest_method' => [
                'required',
                Rule::in([
                    'flat',
                    'declining_balance_equal_installment',
                ]),
            ],

            'term' => ['required', 'integer', 'min:1'],
            'term_unit' => [
                'required',
                Rule::in(['weeks', 'months']),
            ],

            'repayment_order' => [
                'required',
                Rule::in([
                    'principal_first',
                    'interest_first',
                ]),
            ],

            'fees' => ['required', 'numeric', 'min:0'],
            'fee_is_recurring' => ['boolean'],
            'is_active' => ['boolean'],
        ]);
    }
}
