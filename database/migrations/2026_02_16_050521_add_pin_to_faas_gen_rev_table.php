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
            if (!Schema::hasColumn('faas_gen_rev', 'pin')) {
                $table->string('pin', 100)->nullable()->after('arpn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_gen_rev', function (Blueprint $table) {
            $table->dropColumn('pin');
        });
    }
};
