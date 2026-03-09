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
        Schema::table('bpls_payments', function (Blueprint $table) {
            $table->dropForeign(['business_entry_id']);
        });

        Schema::table('bpls_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('business_entry_id')->nullable()->change();
            $table->unsignedBigInteger('bpls_application_id')->nullable()->after('business_entry_id');

            $table->foreign('business_entry_id')
                ->references('id')
                ->on('bpls_business_entries')
                ->onDelete('cascade');

            $table->foreign('bpls_application_id')
                ->references('id')
                ->on('bpls_online_applications')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bpls_payments', function (Blueprint $table) {
            $table->dropForeign(['bpls_application_id']);
            $table->dropColumn('bpls_application_id');
            
            $table->dropForeign(['business_entry_id']);
        });

        Schema::table('bpls_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('business_entry_id')->nullable(false)->change();
            $table->foreign('business_entry_id')
                ->references('id')
                ->on('bpls_business_entries')
                ->onDelete('cascade');
        });
    }
};
