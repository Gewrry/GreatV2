<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_info')->cascadeOnDelete();
            $table->date('log_date');
            $table->time('log_time');
            $table->enum('log_type', ['IN', 'OUT'])->default('IN');
            $table->enum('source', ['biometric', 'manual'])->default('biometric');
            $table->timestamps();

            $table->unique(['employee_id', 'log_date', 'log_time'], 'unique_employee_log');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_time_logs');
    }
};
