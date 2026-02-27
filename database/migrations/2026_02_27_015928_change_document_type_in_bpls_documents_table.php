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
        Schema::table('bpls_documents', function (Blueprint $table) {
            $table->string('document_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bpls_documents', function (Blueprint $table) {
            $table->enum('document_type', [
                'dti_sec_cda',
                'barangay_clearance',
                'lease_contract',
                'owners_consent',
                'community_tax_certificate',
                'fire_clearance',
                'sanitary_permit',
                'others',
            ])->change();
        });
    }
};
