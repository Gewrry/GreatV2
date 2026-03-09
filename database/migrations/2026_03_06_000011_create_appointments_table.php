<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique();
            $table->unsignedBigInteger('applicant_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('plantilla_id')->nullable();
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('employment_type_id');
            $table->unsignedBigInteger('salary_grade_id');
            $table->tinyInteger('salary_step')->default(1);
            $table->string('position_title');
            $table->string('appointment_type', 50);
            $table->date('effectivity_date');
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('probationary');
            $table->text('place_of_work')->nullable();
            $table->string('funding_source', 255)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('set null');
            // $table->foreign('employee_id')->references('id')->on('employee_info')->onDelete('set null');
            $table->foreign('plantilla_id')->references('id')->on('plantilla')->onDelete('set null');
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->foreign('employment_type_id')->references('id')->on('employment_types')->onDelete('cascade');
            $table->foreign('salary_grade_id')->references('id')->on('salary_grades')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['employee_id', 'status']);
            $table->index(['office_id', 'appointment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
