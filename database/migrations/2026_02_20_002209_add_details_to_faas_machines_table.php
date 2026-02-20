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
        Schema::table('faas_machines', function (Blueprint $table) {
            $table->date('date_acquired')->nullable()->after('year_installed');
            $table->decimal('installation_cost', 15, 2)->default(0)->after('insurance_cost');
            $table->integer('estimated_life')->nullable()->after('market_value');
            $table->integer('remaining_life')->nullable()->after('estimated_life');
            $table->string('condition')->nullable()->after('remaining_life');
            $table->string('supplier_vendor')->nullable()->after('memoranda');
            $table->string('invoice_no')->nullable()->after('supplier_vendor');
            $table->string('funding_source')->nullable()->after('invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_machines', function (Blueprint $table) {
            $table->dropColumn([
                'date_acquired',
                'installation_cost',
                'estimated_life',
                'remaining_life',
                'condition',
                'supplier_vendor',
                'invoice_no',
                'funding_source'
            ]);
        });
    }
};
