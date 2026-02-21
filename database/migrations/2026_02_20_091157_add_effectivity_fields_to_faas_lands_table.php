<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('faas_lands', function (Blueprint $table) {

            if (!Schema::hasColumn('faas_lands', 'effectivity_quarter')) {
                $table->tinyInteger('effectivity_quarter')->nullable()->after('effectivity_date');
            }

            if (!Schema::hasColumn('faas_lands', 'effectivity_year')) {
                $table->integer('effectivity_year')->nullable()->after('effectivity_quarter');
            }

            if (!Schema::hasColumn('faas_lands', 'rev_year')) {
                $table->string('rev_year', 10)->nullable()->after('effectivity_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faas_lands', function (Blueprint $table) {
            $table->dropColumnIfExists('effectivity_quarter');
            $table->dropColumnIfExists('effectivity_year');
            $table->dropColumnIfExists('rev_year');
        });
    }
};