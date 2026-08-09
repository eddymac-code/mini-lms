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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('payment_request_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('amount', 15, 2);

            $table->string('phone', 20);

            $table->string('mpesa_receipt_number', 50)->unique();

            $table->timestamp('transaction_date');

            $table->timestamps();

            $table->index(['loan_id', 'transaction_date']);
            $table->index(['customer_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
