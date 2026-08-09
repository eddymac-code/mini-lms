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
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('loan_installment_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('component', [
                'principal',
                'interest',
                'fee',
            ]);

            $table->decimal('amount', 15, 2);

            $table->timestamps();

            $table->unique(
                ['payment_id', 'loan_installment_id', 'component'],
                'payment_allocations_payment_component_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};
