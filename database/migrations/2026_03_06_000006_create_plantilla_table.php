<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plantilla', function (Blueprint $table) {
            $table->id();
            $table->string('item_number', 50)->unique();
            $table->string('position_title');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('salary_grade_id');
            $table->tinyInteger('salary_step')->default(1);
            $table->unsignedBigInteger('employment_type_id');
            $table->string('position_level', 50)->nullable();
            $table->string('workstation', 255)->nullable();
            $table->string('funding_source', 255)->nullable();
            $table->date('effectivity_date')->nullable();
            $table->boolean('is_vacant')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onDelete('cascade');

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
                ->onDelete('cascade');

            $table->foreign('employment_type_id')
                ->references('id')
                ->on('employment_types')
                ->onDelete('cascade');

            $table->index(['office_id', 'is_vacant']);
            $table->index(['salary_grade_id', 'is_vacant']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla');
    }
};
