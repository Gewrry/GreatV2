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
        Schema::table('faas_lands', function (Blueprint $table) {
            $table->boolean('is_corner_lot')->default(false)->after('longitude');
            $table->string('land_type')->nullable()->after('is_corner_lot'); // e.g., land, road_lot, open_space, alley
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_lands', function (Blueprint $table) {
            $table->dropColumn(['is_corner_lot', 'land_type']);
        });
    }
};
