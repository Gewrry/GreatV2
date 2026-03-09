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
        Schema::create('hr_salary_grades', function (Blueprint $table) {
            $table->id();
            $table->integer('grade');
            $table->decimal('step_1', 10, 2);
            $table->decimal('step_2', 10, 2);
            $table->decimal('step_3', 10, 2);
            $table->decimal('step_4', 10, 2);
            $table->decimal('step_5', 10, 2);
            $table->decimal('step_6', 10, 2);
            $table->decimal('step_7', 10, 2);
            $table->decimal('step_8', 10, 2);
            $table->string('implementation_year', 4);
            $table->timestamps();

            $table->unique(['grade', 'implementation_year'], 'hr_sg_grade_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_salary_grades');
    }
};
