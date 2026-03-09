<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpls_application_ors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpls_application_id')
                  ->constrained('bpls_applications')
                  ->cascadeOnDelete();
            $table->foreignId('or_assignment_id')
                  ->constrained('or_assignments')
                  ->cascadeOnDelete();
            $table->string('or_number');
            $table->unsignedTinyInteger('installment_number'); // 1–4
            $table->string('period_label');                    // e.g. "Q1 2025"
            $table->string('status')->default('unpaid');       // unpaid | paid
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique('or_number'); // prevent double-allocation
            $table->index('bpls_application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpls_application_ors');
    }
};