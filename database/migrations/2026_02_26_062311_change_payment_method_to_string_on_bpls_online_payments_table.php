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
        Schema::table('bpls_online_payments', function (Blueprint $table) {
            $table->string('payment_method')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bpls_online_payments', function (Blueprint $table) {
            $table->enum('payment_method', ['gcash', 'maya', 'landbank', 'over_the_counter'])->change();
        });
    }
};
