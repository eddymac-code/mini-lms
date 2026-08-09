<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\MpesaCallbackController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('users', UserController::class)->names([
        'index' => 'users',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);

    Route::resource('customers', CustomerController::class)->names([
        'index' => 'customers',
        'create' => 'customers.create',
        'store' => 'customers.store',
        'show' => 'customers.show',
        'edit' => 'customers.edit',
        'update' => 'customers.update',
        'destroy' => 'customers.destroy',
    ]);

    Route::resource('loan-products', LoanProductController::class)->names([
        'index' => 'loan-products',
        'create' => 'loan-products.create',
        'store' => 'loan-products.store',
        'show' => 'loan-products.show',
        'edit' => 'loan-products.edit',
        'update' => 'loan-products.update',
        'destroy' => 'loan-products.destroy',
    ]);

    Route::get('loans', [LoanController::class, 'index'])
        ->name('loans');

    Route::get('loans/create', [LoanController::class, 'create'])
        ->name('loans.create');

    Route::post('loans', [LoanController::class, 'store'])
        ->name('loans.store');

    Route::get('loans/{loan}', [LoanController::class, 'show'])
        ->name('loans.show');

    Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])
        ->name('loans.approve');

    Route::post('loans/{loan}/disburse', [LoanController::class, 'disburse'])
        ->name('loans.disburse');

    Route::get('payments', [PaymentController::class, 'index'])->name('repayments');

    Route::post('loans/{loan}/payments', [PaymentController::class, 'store'])->name('payments.store');
});

Route::post(
    'api/bridge/stk/callback',
    [MpesaCallbackController::class, 'stk']
)->name('mpesa.stk.callback');

Route::post(
    'api/bridge/b2c/callback',
    [MpesaCallbackController::class, 'b2cResult']
)->name('mpesa.b2c.callback');

Route::post(
    'api/bridge/b2c/timeout',
    [MpesaCallbackController::class, 'b2cTimeout']
)->name('mpesa.b2c.callback');

require __DIR__.'/settings.php';
