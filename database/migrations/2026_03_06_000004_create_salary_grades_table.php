<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_grades', function (Blueprint $table) {
            $table->id();
            $table->integer('grade_number')->unique();
            $table->string('grade_name', 50)->nullable();
            $table->decimal('step_1', 15, 2)->nullable();
            $table->decimal('step_2', 15, 2)->nullable();
            $table->decimal('step_3', 15, 2)->nullable();
            $table->decimal('step_4', 15, 2)->nullable();
            $table->decimal('step_5', 15, 2)->nullable();
            $table->decimal('step_6', 15, 2)->nullable();
            $table->decimal('step_7', 15, 2)->nullable();
            $table->decimal('step_8', 15, 2)->nullable();
            $table->string('salary_schedule', 50)->nullable();
            $table->integer('effectivity_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_grades');
    }
};
