<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);            // e.g. Vacation Leave, Sick Leave
            $table->string('code', 20)->unique();    // e.g. VL, SL, FL, SPL
            $table->text('description')->nullable();
            $table->decimal('max_days_per_year', 5, 2)->default(0); // max earnable per year
            $table->boolean('is_convertible')->default(false);      // can be monetized
            $table->boolean('requires_medical')->default(false);    // needs medical cert
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_leave_types');
    }
};
