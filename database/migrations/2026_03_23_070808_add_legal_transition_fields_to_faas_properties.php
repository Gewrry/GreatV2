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
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->string('instrument_type')->nullable()->after('transfer_tax_receipt_date')->comment('Deed of Sale, Donation, etc.');
            $table->date('instrument_date')->nullable()->after('instrument_type');
            $table->decimal('consideration_amount', 18, 2)->nullable()->after('instrument_date')->comment('Sale Price / Value');
            $table->string('rd_entry_no')->nullable()->after('consideration_amount')->comment('Registry of Deeds Entry Number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropColumn(['instrument_type', 'instrument_date', 'consideration_amount', 'rd_entry_no']);
        });
    }
};
