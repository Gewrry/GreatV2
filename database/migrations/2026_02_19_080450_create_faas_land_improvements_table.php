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
        Schema::create('faas_land_improvements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained('faas_lands')->onDelete('cascade');
            $table->foreignId('improvement_id')->constrained('rpta_other_improvement');
            $table->decimal('quantity', 15, 2)->default(1);
            $table->decimal('unit_value', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faas_land_improvements');
    }
};
