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
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->foreignId('parent_land_faas_id')
                ->nullable()
                ->after('property_type')
                ->constrained('faas_properties')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropForeign(['parent_land_faas_id']);
            $table->dropColumn('parent_land_faas_id');
        });
    }
};
