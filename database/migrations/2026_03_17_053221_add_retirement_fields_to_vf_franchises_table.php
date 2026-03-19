<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vf_franchises', function (Blueprint $table) {
            $table->string('retirement_reason')->nullable()->after('status');
            $table->date('retirement_date')->nullable()->after('retirement_reason');
            $table->text('retirement_remarks')->nullable()->after('retirement_date');
            $table->timestamp('retired_at')->nullable()->after('retirement_remarks');
            $table->foreignId('retired_by')->nullable()->after('retired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vf_franchises', function (Blueprint $table) {
            //
        });
    }
};
