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
        Schema::table('faas_lands', function (Blueprint $table) {
            $table->string('arpn')->nullable()->after('pin');
        });
        Schema::table('faas_buildings', function (Blueprint $table) {
            $table->string('arpn')->nullable()->after('pin');
        });
        Schema::table('faas_machines', function (Blueprint $table) {
            $table->string('arpn')->nullable()->after('pin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_lands', function (Blueprint $table) {
            $table->dropColumn('arpn');
        });
        Schema::table('faas_buildings', function (Blueprint $table) {
            $table->dropColumn('arpn');
        });
        Schema::table('faas_machines', function (Blueprint $table) {
            $table->dropColumn('arpn');
        });
    }
};
