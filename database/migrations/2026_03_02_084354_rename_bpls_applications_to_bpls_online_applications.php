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
Schema::rename('bpls_applications', 'bpls_online_applications');
            //

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bpls_online_applications', function (Blueprint $table) {
            //
        });
    }
};
