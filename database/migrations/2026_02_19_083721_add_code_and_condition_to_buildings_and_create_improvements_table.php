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
        Schema::table('faas_buildings', function (Blueprint $table) {
            $table->string('building_code')->nullable()->after('faas_id');
            $table->string('condition')->nullable()->after('status');
        });

        Schema::create('faas_building_improvements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('faas_buildings')->onDelete('cascade');
            $table->foreignId('improvement_id')->constrained('rpta_other_improvement');
            $table->decimal('quantity', 18, 2);
            $table->decimal('unit_value', 18, 2);
            $table->decimal('total_value', 18, 2);
            $table->decimal('depreciation_rate', 18, 2)->default(0);
            $table->decimal('remaining_value_percent', 18, 2)->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faas_building_improvements');
        Schema::table('faas_buildings', function (Blueprint $table) {
            $table->dropColumn(['building_code', 'condition']);
        });
    }
};
