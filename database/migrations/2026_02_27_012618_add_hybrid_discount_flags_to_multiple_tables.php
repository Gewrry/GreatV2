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
        Schema::table('bpls_owners', function (Blueprint $table) {
            $table->boolean('is_bmbe')->default(false)->after('is_senior');
            $table->boolean('is_cooperative')->default(false)->after('is_bmbe');
        });

        Schema::table('bpls_business_entries', function (Blueprint $table) {
            $table->boolean('is_bmbe')->default(false)->after('is_senior');
            $table->boolean('is_cooperative')->default(false)->after('is_bmbe');
        });

        Schema::table('bpls_applications', function (Blueprint $table) {
            $table->boolean('discount_claimed')->default(false)->after('application_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bpls_owners', function (Blueprint $table) {
            $table->dropColumn(['is_bmbe', 'is_cooperative']);
        });

        Schema::table('bpls_business_entries', function (Blueprint $table) {
            $table->dropColumn(['is_bmbe', 'is_cooperative']);
        });

        Schema::table('bpls_applications', function (Blueprint $table) {
            $table->dropColumn('discount_claimed');
        });
    }
};
