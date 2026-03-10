<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bpls_businesses', function (Blueprint $table) {
            $table->decimal('capital_investment', 15, 2)->nullable()->after('business_nature');
        });
    }

    public function down(): void
    {
        Schema::table('bpls_businesses', function (Blueprint $table) {
            $table->dropColumn('capital_investment');
        });
    }
};
