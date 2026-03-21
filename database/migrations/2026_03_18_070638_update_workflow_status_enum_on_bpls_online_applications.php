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
        // Enums in Laravel Schema builder often require doctrine/dbal. 
        // A raw DB statement is safer for just appending values.
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE bpls_online_applications MODIFY COLUMN workflow_status ENUM('draft', 'submitted', 'returned', 'verified', 'assessed', 'paid', 'approved', 'rejected', 'retirement_requested', 'retired') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE bpls_online_applications MODIFY COLUMN workflow_status ENUM('draft', 'submitted', 'returned', 'verified', 'assessed', 'paid', 'approved', 'rejected') DEFAULT 'draft'");
    }
};
