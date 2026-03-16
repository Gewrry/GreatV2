<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vf_payments', function (Blueprint $table) {
            $table->id();

            // AF51 Reference
            $table->string('or_number')->unique();              // Official Receipt No. (No. A ___)
            $table->date('or_date');                            // DATE field
            $table->string('agency')->nullable();               // AGENCY
            $table->string('fund')->nullable();                 // FUND
            $table->string('payor');                            // PAYOR (auto-filled from owner)

            // Foreign key
            $table->foreignId('franchise_id')->constrained('vf_franchises')->cascadeOnDelete();

            // Payment breakdown (NATURE OF COLLECTION rows)
            $table->json('collection_items');
            // [{ nature: "Franchise Fee", account_code: "1-01-01", amount: 150.00 }, ...]

            $table->decimal('total_amount', 10, 2);             // TOTAL
            $table->string('amount_in_words')->nullable();      // AMOUNT IN WORDS

            // Payment method (bottom section of AF51)
            $table->enum('payment_method', ['cash', 'check', 'money_order'])->default('cash');
            $table->string('drawee_bank')->nullable();          // Drawee Bank
            $table->string('check_mo_number')->nullable();      // Number
            $table->date('check_mo_date')->nullable();          // Date

            // Meta
            $table->string('status')->default('paid');          // paid, voided
            $table->text('remarks')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vf_payments');
    }
};