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
    // Drop the foreign key by its exact name (uses old table name prefix)
    DB::statement('ALTER TABLE bpls_online_applications DROP FOREIGN KEY bpls_applications_business_entry_id_foreign');

    Schema::table('bpls_online_applications', function (Blueprint $table) {
        $table->dropColumn('business_entry_id');
    });
}

public function down(): void
{
    Schema::table('bpls_online_applications', function (Blueprint $table) {
        $table->unsignedBigInteger('business_entry_id')->nullable();
    });
}
};
