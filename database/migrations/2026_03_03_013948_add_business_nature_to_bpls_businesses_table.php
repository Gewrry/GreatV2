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
    Schema::table('bpls_businesses', function (Blueprint $table) {
        $table->string('business_nature')->nullable()->after('type_of_business');
    });
}

public function down(): void
{
    Schema::table('bpls_businesses', function (Blueprint $table) {
        $table->dropColumn('business_nature');
    });
}
};
