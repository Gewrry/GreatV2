<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);          // e.g. "Regular 8-5"
            $table->time('am_in')->default('08:00');
            $table->time('am_out')->default('12:00');
            $table->time('pm_in')->default('13:00');
            $table->time('pm_out')->default('17:00');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_work_schedules');
    }
};
