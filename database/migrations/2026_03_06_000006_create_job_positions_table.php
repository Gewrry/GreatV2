<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('job_positions');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('position_name');
            $table->string('position_code', 30)->unique();
            $table->text('position_description')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('salary_grade_id')->nullable();
            $table->unsignedBigInteger('employment_type_id')->nullable();
            $table->string('position_level', 50)->nullable();
            $table->integer('item_number')->nullable();
            $table->string('workstation', 255)->nullable();
            $table->integer('plantilla_count')->default(1); // fixed: removed leading space
            $table->boolean('is_vacant')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onDelete('set null');

            $table->foreign('division_id')
                ->references('id')
                ->on('divisions')
                ->onDelete('set null');

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');

            $table->foreign('salary_grade_id')
                ->references('id')
                ->on('salary_grades')
                ->onDelete('set null');

            $table->foreign('employment_type_id')
                ->references('id')
                ->on('employment_types')
                ->onDelete('set null');

            $table->index(['office_id', 'division_id', 'department_id']);
        });
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('job_positions');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};