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
        Schema::table('employee_info', function (Blueprint $table) {
            $table->foreignId('plantilla_position_id')->nullable()->constrained('hr_plantilla_positions')->nullOnDelete();
            $table->integer('salary_step')->default(1)->comment('1 through 8');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_info', function (Blueprint $table) {
            $table->dropForeign(['plantilla_position_id']);
            $table->dropColumn(['plantilla_position_id', 'salary_step']);
        });
    }
};
