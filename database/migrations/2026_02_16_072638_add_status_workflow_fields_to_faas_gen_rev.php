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
        Schema::table('faas_gen_rev', function (Blueprint $table) {
            $table->string('kind')->nullable()->after('id'); 
            $table->string('transaction_type')->nullable()->after('kind'); 
            $table->date('inspection_date')->nullable()->after('gen_desc');
            $table->string('inspected_by')->nullable()->after('inspection_date');
            $table->text('inspection_remarks')->nullable()->after('inspected_by');
            $table->unsignedBigInteger('previous_td_id')->nullable()->after('inspection_remarks');
            $table->string('draft_id')->nullable()->after('td_no'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_gen_rev', function (Blueprint $table) {
            //
        });
    }
};
