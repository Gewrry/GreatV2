<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bpls_applications', function (Blueprint $table) {
            // Only add ors_confirmed — assessed_at already exists in the table
            if (!Schema::hasColumn('bpls_applications', 'ors_confirmed')) {
                $table->boolean('ors_confirmed')->default(false)->after('assessment_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpls_applications', function (Blueprint $table) {
            if (Schema::hasColumn('bpls_applications', 'ors_confirmed')) {
                $table->dropColumn('ors_confirmed');
            }
        });
    }
};