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
        Schema::table('bpls_benefits', function (Blueprint $table) {
            $table->string('apply_to')->default('permit_only')->after('discount_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bpls_benefits', function (Blueprint $table) {
            $table->dropColumn('apply_to');
        });
    }
};
