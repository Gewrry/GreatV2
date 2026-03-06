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
            $table->string('property_type')->nullable()->after('revision_type');
        });

        // Sync existing drafts to ensure they show up on the new redesigned page
        $faasRecords = \App\Models\RPT\FaasProperty::whereNotNull('property_registration_id')->get();
        foreach ($faasRecords as $faas) {
            if ($faas->propertyRegistration) {
                $faas->update(['property_type' => $faas->propertyRegistration->property_type]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropColumn('property_type');
        });
    }
};
