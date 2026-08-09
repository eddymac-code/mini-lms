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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('amount', 15, 2);

            $table->string('phone', 20);

            $table->string('reference', 100)->unique();

            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'expired',
            ])->default('pending');

            $table->string('checkout_request_id')->nullable()->unique();
            $table->string('merchant_request_id')->nullable();

            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index(['loan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
