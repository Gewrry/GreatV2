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
        Schema::table('faas_gen_rev', function (Blueprint $table) {
            // Add new TD-specific columns first (before dropping kind)
            if (!Schema::hasColumn('faas_gen_rev', 'td_no')) {
                $table->string('td_no', 50)->nullable()->after('id'); // Will be populated, then made unique
            }
            if (!Schema::hasColumn('faas_gen_rev', 'arpn')) {
                $table->string('arpn', 50)->nullable()->after('td_no');
            }
            if (!Schema::hasColumn('faas_gen_rev', 'total_market_value')) {
                $table->decimal('total_market_value', 15, 2)->default(0)->after('arpn');
            }
            if (!Schema::hasColumn('faas_gen_rev', 'total_assessed_value')) {
                $table->decimal('total_assessed_value', 15, 2)->default(0)->after('total_market_value');
            }
        });

        // Migrate existing data - copy td_no from component tables to parent
        DB::statement('
            UPDATE faas_gen_rev fg
            LEFT JOIN faas_lands fl ON fg.id = fl.faas_id
            LEFT JOIN faas_buildings fb ON fg.id = fb.faas_id
            LEFT JOIN faas_machines fm ON fg.id = fm.faas_id
            SET fg.td_no = COALESCE(fl.td_no, fb.td_no, fm.td_no),
                fg.arpn = COALESCE(fl.arpn, fb.arpn, fm.arpn)
            WHERE fg.td_no IS NULL
        ');

        // Generate TD numbers for any records that still don't have one
        DB::statement("
            UPDATE faas_gen_rev 
            SET td_no = CONCAT('TD-', LPAD(id, 8, '0'))
            WHERE td_no IS NULL OR td_no = ''
        ");

        // Now make td_no unique
        Schema::table('faas_gen_rev', function (Blueprint $table) {
            $table->unique('td_no');
        });

        // Remove the 'kind' column - no longer needed as TD can have multiple component types
        Schema::table('faas_gen_rev', function (Blueprint $table) {
            if (Schema::hasColumn('faas_gen_rev', 'kind')) {
                $table->dropColumn('kind');
            }
        });

        // Update component tables - remove td_no, pin, arpn (now inherited from parent TD)
        Schema::table('faas_lands', function (Blueprint $table) {
            if (Schema::hasColumn('faas_lands', 'td_no')) {
                $table->dropColumn('td_no');
            }
            if (Schema::hasColumn('faas_lands', 'pin')) {
                $table->dropColumn('pin');
            }
            if (Schema::hasColumn('faas_lands', 'arpn')) {
                $table->dropColumn('arpn');
            }
        });

        Schema::table('faas_buildings', function (Blueprint $table) {
            if (Schema::hasColumn('faas_buildings', 'td_no')) {
                $table->dropColumn('td_no');
            }
            if (Schema::hasColumn('faas_buildings', 'pin')) {
                $table->dropColumn('pin');
            }
            if (Schema::hasColumn('faas_buildings', 'arpn')) {
                $table->dropColumn('arpn');
            }
        });

        Schema::table('faas_machines', function (Blueprint $table) {
            if (Schema::hasColumn('faas_machines', 'td_no')) {
                $table->dropColumn('td_no');
            }
            if (Schema::hasColumn('faas_machines', 'pin')) {
                $table->dropColumn('pin');
            }
            if (Schema::hasColumn('faas_machines', 'arpn')) {
                $table->dropColumn('arpn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_gen_rev', function (Blueprint $table) {
            // Restore original structure
            $table->string('kind')->nullable()->after('id');
            $table->dropUnique(['td_no']);
            $table->dropColumn(['td_no', 'arpn', 'total_market_value', 'total_assessed_value']);
        });

        // Restore component table columns
        Schema::table('faas_lands', function (Blueprint $table) {
            $table->string('td_no')->nullable()->after('faas_id');
            $table->string('pin')->nullable()->after('td_no');
            $table->string('arpn')->nullable()->after('pin');
        });

        Schema::table('faas_buildings', function (Blueprint $table) {
            $table->string('td_no')->nullable()->after('faas_id');
            $table->string('pin')->nullable()->after('td_no');
            $table->string('arpn')->nullable()->after('pin');
        });

        Schema::table('faas_machines', function (Blueprint $table) {
            $table->string('td_no')->nullable()->after('faas_id');
            $table->string('pin')->nullable()->after('td_no');
            $table->string('arpn')->nullable()->after('pin');
        });
    }
};
