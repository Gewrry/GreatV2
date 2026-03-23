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
        Schema::table('rpt_billings', function (Blueprint $table) {
            $table->string('billing_type', 20)->default('tax')->after('tax_declaration_id');
            $table->integer('quarter')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rpt_billings', function (Blueprint $table) {
            $table->dropColumn('billing_type');
        });
    }
};
