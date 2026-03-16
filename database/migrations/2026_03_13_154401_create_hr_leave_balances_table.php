<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_info')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('hr_leave_types')->cascadeOnDelete();
            $table->year('year');
            $table->decimal('earned', 6, 2)->default(0);       // total credits earned
            $table->decimal('used', 6, 2)->default(0);          // total credits consumed
            $table->decimal('carry_over', 6, 2)->default(0);    // carried from previous year
            $table->timestamps();

            $table->unique(['employee_id', 'leave_type_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_leave_balances');
    }
};
