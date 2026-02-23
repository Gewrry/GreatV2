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
            $table->string('revision_type')->nullable()->after('td_no');
            $table->text('reason')->nullable()->after('revision_type');
            $table->text('memoranda')->nullable()->after('reason');
            $table->integer('effectivity_quarter')->nullable()->after('memoranda');
            $table->integer('effectivity_year')->nullable()->after('effectivity_quarter');
            $table->string('approved_by')->nullable()->after('effectivity_year');
            $table->date('date_approved')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_gen_rev', function (Blueprint $table) {
            $table->dropColumn([
                'revision_type',
                'reason',
                'memoranda',
                'effectivity_quarter',
                'effectivity_year',
                'approved_by',
                'date_approved'
            ]);
        });
    }
};
