<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->decimal('min_principal', 15, 2);
            $table->decimal('max_principal', 15, 2);
            $table->decimal('default_principal', 15, 2);

            $table->decimal('interest_rate', 8, 4);

            $table->enum('interest_method', [
                'flat',
                'declining_balance_equal_installment',
            ]);

            $table->unsignedInteger('term');

            $table->enum('term_unit', [
                'weeks',
                'months',
            ]);

            $table->enum('repayment_order', [
                'principal_first',
                'interest_first',
            ])->default('principal_first');

            $table->decimal('fees', 15, 2)->default(0);
            $table->boolean('fee_is_recurring')->default(false);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_products');
    }
};
