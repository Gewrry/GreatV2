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
        // Add 'recommended' to the status ENUM for faas_properties
        DB::statement("ALTER TABLE faas_properties MODIFY COLUMN status ENUM('draft', 'for_review', 'recommended', 'approved', 'cancelled', 'inactive') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'recommended' from the status ENUM
        DB::statement("ALTER TABLE faas_properties MODIFY COLUMN status ENUM('draft', 'for_review', 'approved', 'cancelled', 'inactive') DEFAULT 'draft'");
    }
};
