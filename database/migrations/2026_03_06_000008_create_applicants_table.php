<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_vacancy_id');
            $table->string('application_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email');
            $table->string('contact_number', 50)->nullable();
            $table->text('address')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('civil_status', 50)->nullable();
            $table->string('education', 255)->nullable();
            $table->text('work_experience')->nullable();
            $table->string('eligibility', 255)->nullable();
            $table->enum('status', ['pending', 'screening', 'interview', 'selected', 'not_selected', 'withdrawn', 'appointed'])->default('pending');
            $table->text('remarks')->nullable();
            $table->date('application_date')->nullable();
            $table->timestamps();

            $table->foreign('job_vacancy_id')->references('id')->on('job_vacancies')->onDelete('cascade');
            $table->index(['job_vacancy_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
