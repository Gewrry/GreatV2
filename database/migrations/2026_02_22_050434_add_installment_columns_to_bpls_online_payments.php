<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bpls_online_payments', function (Blueprint $table) {
            // Installment tracking
            $table->unsignedTinyInteger('installment_number')
                  ->default(1)
                  ->after('payment_year');

            $table->unsignedTinyInteger('installment_total')
                  ->default(1)
                  ->after('installment_number');

            // PayMongo
            $table->string('paymongo_payment_intent_id')
                  ->nullable()
                  ->after('gateway_transaction_id');

            $table->string('paymongo_checkout_url')
                  ->nullable()
                  ->after('paymongo_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::table('bpls_online_payments', function (Blueprint $table) {
            $table->dropColumn([
                'installment_number',
                'installment_total',
                'paymongo_payment_intent_id',
                'paymongo_checkout_url',
            ]);
        });
    }
};