<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_daily_time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_info')->cascadeOnDelete();
            $table->date('record_date');
            $table->time('am_in')->nullable();
            $table->time('am_out')->nullable();
            $table->time('pm_in')->nullable();
            $table->time('pm_out')->nullable();
            $table->integer('tardiness_minutes')->default(0);
            $table->integer('undertime_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);
            $table->boolean('is_absent')->default(false);
            $table->string('remarks', 255)->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'record_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_daily_time_records');
    }
};
