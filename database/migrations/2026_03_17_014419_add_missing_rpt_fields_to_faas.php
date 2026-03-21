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
            if (!Schema::hasColumn('faas_properties', 'is_taxable')) {
                $table->boolean('is_taxable')->default(true)->after('property_type');
            }
            if (!Schema::hasColumn('faas_properties', 'effectivity_quarter')) {
                $table->string('effectivity_quarter')->nullable()->after('effectivity_date');
            }
            if (!Schema::hasColumn('faas_properties', 'administrator_tin')) {
                $table->string('administrator_tin')->nullable()->after('administrator_name');
            }
            if (!Schema::hasColumn('faas_properties', 'administrator_contact')) {
                $table->string('administrator_contact')->nullable()->after('administrator_address');
            }
            if (!Schema::hasColumn('faas_properties', 'district')) {
                $table->string('district')->nullable()->after('barangay_id');
            }
            if (!Schema::hasColumn('faas_properties', 'recommended_by')) {
                $table->foreignId('recommended_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('faas_properties', 'date_recommended')) {
                $table->timestamp('date_recommended')->nullable()->after('recommended_by');
            }
            // Legacy / Previous tracking fields for manual encoding
            if (!Schema::hasColumn('faas_properties', 'previous_owner')) {
                $table->string('previous_owner')->nullable()->after('previous_faas_property_id');
            }
            if (!Schema::hasColumn('faas_properties', 'previous_arp_no')) {
                $table->string('previous_arp_no')->nullable()->after('previous_owner');
            }
            if (!Schema::hasColumn('faas_properties', 'previous_assessed_value')) {
                $table->decimal('previous_assessed_value', 18, 2)->nullable()->after('previous_arp_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            if (Schema::hasColumn('faas_properties', 'recommended_by')) {
                $table->dropForeign(['recommended_by']);
            }
            
            $cols = [
                'is_taxable',
                'effectivity_quarter',
                'administrator_tin',
                'administrator_contact',
                'district',
                'recommended_by',
                'date_recommended',
                'previous_owner',
                'previous_arp_no',
                'previous_assessed_value',
            ];
            
            foreach($cols as $col) {
                if(Schema::hasColumn('faas_properties', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
