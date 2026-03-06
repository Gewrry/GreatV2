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
            $table->foreignId('property_registration_id')->nullable()->after('id')->constrained('rpt_property_registrations')->nullOnDelete();
            $table->date('effectivity_date')->nullable()->after('revision_year_id');
            $table->string('revision_type')->nullable()->after('effectivity_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropForeign(['property_registration_id']);
            $table->dropColumn(['property_registration_id', 'effectivity_date', 'revision_type']);
        });
    }
};
