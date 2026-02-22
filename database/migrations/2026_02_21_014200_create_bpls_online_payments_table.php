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
        Schema::create('bpls_online_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpls_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bpls_assessment_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number')->unique();
            $table->decimal('amount_paid', 12, 2);
            $table->integer('payment_year')->default(2026);
            $table->integer('renewal_cycle')->default(0); // matches BusinessEntry.renewal_cycle
            $table->enum('payment_method', ['gcash', 'maya', 'landbank', 'over_the_counter']);
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpls_payments');
    }
};
