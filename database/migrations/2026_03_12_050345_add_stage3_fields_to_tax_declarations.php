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
        Schema::table('tax_declarations', function (Blueprint $table) {
            $table->unsignedTinyInteger('effectivity_quarter')->default(1)->after('effectivity_year');
            $table->string('exemption_basis')->nullable()->after('is_taxable');
            $table->string('cancelled_td_no')->nullable()->after('prev_td_no');
            $table->text('cancellation_reason')->nullable()->after('cancelled_td_no');
        });
    }

    public function down(): void
    {
        Schema::table('tax_declarations', function (Blueprint $table) {
            $table->dropColumn(['effectivity_quarter', 'exemption_basis', 'cancelled_td_no', 'cancellation_reason']);
        });
    }
};
