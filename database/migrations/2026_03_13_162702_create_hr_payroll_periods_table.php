<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_name', 100);
            $table->date('date_from');
            $table->date('date_to');
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_periods');
    }
};
