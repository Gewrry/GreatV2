<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── RPT BILLING ───────────────────────────────────────────────────────

        Schema::create('rpt_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_declaration_id')->constrained('tax_declarations')->cascadeOnDelete();
            $table->year('tax_year');
            $table->integer('quarter')->nullable()->comment('1-4 for quarterly, null for annual');

            // Computed amounts
            $table->decimal('basic_tax', 18, 2)->default(0);
            $table->decimal('sef_tax', 18, 2)->default(0)->comment('Special Education Fund');
            $table->decimal('total_tax_due', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0)->comment('Early payment discount');
            $table->decimal('penalty_amount', 18, 2)->default(0);
            $table->decimal('total_amount_due', 18, 2)->default(0);
            $table->decimal('amount_paid', 18, 2)->default(0);
            $table->decimal('balance', 18, 2)->default(0);

            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });

        // Payment records tied to billing
        Schema::create('rpt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpt_billing_id')->constrained('rpt_billings')->cascadeOnDelete();
            $table->string('or_no')->nullable()->comment('Official Receipt Number');
            $table->decimal('amount', 18, 2);
            $table->decimal('basic_tax', 18, 2)->default(0);
            $table->decimal('sef_tax', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('penalty', 18, 2)->default(0);
            $table->enum('payment_mode', ['cash', 'check', 'gcash', 'others'])->default('cash');
            $table->string('check_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->date('payment_date');
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpt_payments');
        Schema::dropIfExists('rpt_billings');
    }
};
