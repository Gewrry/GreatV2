<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add 'inactive' to the status ENUM for faas_properties
        // In MySQL, we use raw SQL to modify ENUMs efficiently
        DB::statement("ALTER TABLE faas_properties MODIFY COLUMN status ENUM('draft', 'for_review', 'approved', 'cancelled', 'inactive') DEFAULT 'draft'");

        // 2. Add inactive_at timestamp
        Schema::table('faas_properties', function (Blueprint $table) {
            if (!Schema::hasColumn('faas_properties', 'inactive_at')) {
                $table->timestamp('inactive_at')->nullable()->after('approved_at');
            }
        });

        // 3. Optional: Add 'inactive' to tax_declarations for future revisions
        DB::statement("ALTER TABLE tax_declarations MODIFY COLUMN status ENUM('draft', 'for_review', 'approved', 'forwarded', 'cancelled', 'inactive') DEFAULT 'draft'");
        
        Schema::table('tax_declarations', function (Blueprint $table) {
            if (!Schema::hasColumn('tax_declarations', 'inactive_at')) {
                $table->timestamp('inactive_at')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropColumn('inactive_at');
        });

        DB::statement("ALTER TABLE faas_properties MODIFY COLUMN status ENUM('draft', 'for_review', 'approved', 'cancelled') DEFAULT 'draft'");

        Schema::table('tax_declarations', function (Blueprint $table) {
            $table->dropColumn('inactive_at');
        });

        DB::statement("ALTER TABLE tax_declarations MODIFY COLUMN status ENUM('draft', 'for_review', 'approved', 'forwarded', 'cancelled') DEFAULT 'draft'");
    }
};
