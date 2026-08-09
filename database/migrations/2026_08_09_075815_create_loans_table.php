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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('loan_product_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('principal', 15, 2);

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
            ]);

            $table->enum('status', [
                'pending',
                'approved',
                'disbursed',
                'active',
                'closed',
            ])->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('disbursed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('disbursed_at')->nullable();

            $table->timestamps();

            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
