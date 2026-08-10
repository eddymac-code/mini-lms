<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'customers' => Customer::count(),

                'loan_products' => LoanProduct::where(
                    'is_active',
                    true
                )->count(),

                'users' => User::count(),

                'disbursed_loans' => Loan::whereIn('status', [
                    'disbursed',
                    'active',
                    'closed',
                ])->count(),
            ],

            'recentLoans' => Loan::with([
                'customer:id,name',
                'loanProduct:id,name',
            ])
                ->latest()
                ->take(5)
                ->get([
                    'id',
                    'customer_id',
                    'loan_product_id',
                    'principal',
                    'status',
                    'created_at',
                ]),
        ]);
    }
}
