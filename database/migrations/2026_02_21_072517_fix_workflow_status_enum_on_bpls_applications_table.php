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
        DB::statement("ALTER TABLE bpls_applications MODIFY COLUMN workflow_status 
        ENUM(
            'draft',
            'submitted',
            'returned',
            'verified',
            'assessed',
            'paid',
            'approved',
            'rejected'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        // revert to whatever you had before
        DB::statement("ALTER TABLE bpls_applications MODIFY COLUMN workflow_status 
        ENUM(
            'draft',
            'submitted',
            'returned',
            'approved',
            'rejected'
        ) NOT NULL DEFAULT 'draft'");
    }
};
