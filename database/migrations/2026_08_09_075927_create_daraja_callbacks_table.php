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
        Schema::create('daraja_callbacks', function (Blueprint $table) {
            $table->id();

            $table->string('type', 50);

            $table->json('payload');

            $table->string('checkout_request_id')->nullable();
            $table->string('merchant_request_id')->nullable();
            $table->string('mpesa_receipt_number', 50)->nullable();

            $table->timestamp('received_at');

            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->index('checkout_request_id');
            $table->index('merchant_request_id');
            $table->index('mpesa_receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daraja_callbacks');
    }
};
