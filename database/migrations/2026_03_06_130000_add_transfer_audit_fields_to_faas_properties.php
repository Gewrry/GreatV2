<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            // Audit fields for legal transition documents
            $table->string('car_no')->nullable()->after('previous_faas_property_id')->comment('BIR Certificate Authorizing Registration');
            $table->date('car_date')->nullable()->after('car_no');
            $table->string('transfer_tax_receipt_no')->nullable()->after('car_date')->comment('Local Transfer Tax Receipt');
            $table->date('transfer_tax_receipt_date')->nullable()->after('transfer_tax_receipt_no');
        });
    }

    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropColumn(['car_no', 'car_date', 'transfer_tax_receipt_no', 'transfer_tax_receipt_date']);
        });
    }
};
