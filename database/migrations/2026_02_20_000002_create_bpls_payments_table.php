<?php
// database/migrations/2026_02_20_000002_create_bpls_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bpls_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_entry_id');
            $table->string('or_number', 50);
            $table->date('payment_date');
            $table->json('quarters_paid');           // e.g. [1], [2], [1,2], etc.
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('surcharges', 15, 2)->default(0);
            $table->decimal('backtaxes', 15, 2)->default(0);
            $table->decimal('total_collected', 15, 2);
            $table->string('payment_method')->default('cash'); // cash | check | money_order
            $table->string('drawee_bank', 255)->nullable();
            $table->string('check_number', 50)->nullable();
            $table->date('check_date')->nullable();
            $table->string('fund_code', 20)->default('100');
            $table->string('payor', 255)->nullable();
            $table->text('remarks')->nullable();
            $table->string('received_by', 255)->nullable();
            $table->timestamps();

            $table->foreign('business_entry_id')
                ->references('id')
                ->on('bpls_business_entries')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpls_payments');
    }
};