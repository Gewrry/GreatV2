<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rpt_road_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('rpt_location_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Seed initial data
        DB::table('rpt_road_types')->insert([
            ['name' => 'National', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Provincial', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Municipal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barangay', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('rpt_location_classes')->insert([
            ['name' => 'Prime', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secondary', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Interior', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpt_road_types');
        Schema::dropIfExists('rpt_location_classes');
    }
};
