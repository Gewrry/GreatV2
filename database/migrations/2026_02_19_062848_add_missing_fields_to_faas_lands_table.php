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
            $table->string('block')->nullable()->after('lot_no');
            $table->string('use_restrictions')->nullable()->after('zoning');
            $table->unsignedBigInteger('improvement_kind_id')->nullable()->after('remarks');

            $table->foreign('improvement_kind_id')
                  ->references('id')
                  ->on('rpta_other_improvement')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_lands', function (Blueprint $table) {
            $table->dropForeign(['improvement_kind_id']);
            $table->dropColumn(['block', 'use_restrictions', 'improvement_kind_id']);
        });
    }
};
