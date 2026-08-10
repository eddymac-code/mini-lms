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
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->foreignId('loan_installment_id')
                ->after('customer_id')
                ->constrained('loan_installments')
                ->restrictOnDelete();

            $table->index([
                'loan_installment_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropForeign(['loan_installment_id']);
            $table->dropIndex([
                'payment_requests_loan_installment_id_status_index',
            ]);
            $table->dropColumn('loan_installment_id');
        });
    }
};
