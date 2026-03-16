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
        Schema::table('bpls_owners', function (Blueprint $table) {
            $table->boolean('is_vaccine')->default(false)->after('is_cooperative');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bpls_owners', function (Blueprint $table) {
            $table->dropColumn('is_vaccine');
        });
    }
};
