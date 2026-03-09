<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('vacancy_title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('plantilla_id')->nullable();
            $table->unsignedBigInteger('salary_grade_id')->nullable();
            $table->integer('number_of_positions')->default(1);
            $table->string('position_level', 50)->nullable();
            $table->text('qualifications')->nullable();
            $table->text('duties_and_responsibilities')->nullable();
            $table->date('posting_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->foreign('plantilla_id')->references('id')->on('plantilla')->onDelete('set null');
            $table->foreign('salary_grade_id')->references('id')->on('salary_grades')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
