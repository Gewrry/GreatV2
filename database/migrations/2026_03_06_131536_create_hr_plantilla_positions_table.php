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
        Schema::create('hr_plantilla_positions', function (Blueprint $table) {
            $table->id();
            $table->string('item_number')->unique();
            $table->string('position_title');
            $table->foreignId('salary_grade_id')->constrained('hr_salary_grades')->restrictOnDelete();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('office_id')->nullable()->constrained('offices')->restrictOnDelete();
            $table->enum('employment_status', ['Permanent', 'Casual', 'Co-terminous', 'Elective'])->default('Permanent');
            $table->boolean('is_filled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_plantilla_positions');
    }
};
