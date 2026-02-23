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
        DB::statement("ALTER TABLE bpls_documents MODIFY COLUMN document_type 
        ENUM(
            'dti_sec_cda',
            'barangay_clearance',
            'community_tax',
            'lease_contract',
            'fire_clearance',
            'sanitary_permit',
            'others'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bpls_documents MODIFY COLUMN document_type 
        ENUM(
            'dti_sec_cda',
            'barangay_clearance',
            'lease_contract',
            'fire_clearance',
            'sanitary_permit',
            'others'
        ) NOT NULL");
    }
};
