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
        Schema::table('bpls_online_applications', function (Blueprint $table) {
            $table->string('retirement_reason', 1000)->nullable();
            $table->date('retirement_date')->nullable();
            $table->string('retirement_remarks', 1000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bpls_online_applications', function (Blueprint $table) {
            $table->dropColumn(['retirement_reason', 'retirement_date', 'retirement_remarks']);
        });
    }
};
