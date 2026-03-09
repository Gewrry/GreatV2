<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_government_ids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('id_type', 50);
            $table->string('id_number', 100);
            $table->date('date_issued')->nullable();
            $table->date('date_expiry')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_family_background', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('relation', 50);
            $table->string('name', 255);
            $table->date('birthday')->nullable();
            $table->string('occupation', 255)->nullable();
            $table->string('employer', 255)->nullable();
            $table->text('address')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('level', 100);
            $table->string('school_name', 255);
            $table->string('degree', 255)->nullable();
            $table->string('year_graduated', 20)->nullable();
            $table->string('units_earned', 50)->nullable();
            $table->string('attendance_from', 20)->nullable();
            $table->string('attendance_to', 20)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_civil_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('eligibility', 255);
            $table->string('level', 100)->nullable();
            $table->string('exam_date', 50)->nullable();
            $table->string('exam_place', 255)->nullable();
            $table->string('license_number', 100)->nullable();
            $table->date('license_date_valid')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_work_experience', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('position_title', 255);
            $table->string('company_name', 255);
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('salary', 50)->nullable();
            $table->string('pay_grade', 20)->nullable();
            $table->string('status_of_employment', 100)->nullable();
            $table->boolean('is_government')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('document_type', 100);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 50)->nullable();
            $table->integer('file_size')->nullable();
            $table->date('document_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_trainings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('training_title', 255);
            $table->string('training_type', 100)->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->integer('hours')->nullable();
            $table->string('conducted_by', 255)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_trainings');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_work_experience');
        Schema::dropIfExists('employee_civil_service');
        Schema::dropIfExists('employee_education');
        Schema::dropIfExists('employee_family_background');
        Schema::dropIfExists('employee_government_ids');
    }
};
