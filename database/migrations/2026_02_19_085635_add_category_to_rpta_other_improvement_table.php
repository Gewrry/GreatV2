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
        Schema::table('rpta_other_improvement', function (Blueprint $table) {
            $table->string('category', 20)->nullable()->after('kind_name')->comment('LAND, BUILDING, or NULL (both)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpta_other_improvement', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
