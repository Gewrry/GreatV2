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
            if (!Schema::hasColumn('faas_lands', 'memoranda')) {
                $table->text('memoranda')->nullable()->after('remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_lands', function (Blueprint $table) {
            if (Schema::hasColumn('faas_lands', 'memoranda')) {
                $table->dropColumn('memoranda');
            }
        });
    }
};
