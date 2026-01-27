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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('dep_code', 50)->nullable()->after('department_name');
            $table->string('dep_desc')->nullable()->after('dep_code');
            $table->string('category', 100)->nullable()->after('dep_desc');
            $table->string('sector', 100)->nullable()->after('category');
            $table->integer('rank_order')->nullable()->after('sector');
            $table->string('pay_name', 100)->nullable()->after('rank_order');
            $table->string('pay_full')->nullable()->after('pay_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn([
                'dep_code',
                'dep_desc',
                'category',
                'sector',
                'rank_order',
                'pay_name',
                'pay_full',
            ]);
        });
    }
};
