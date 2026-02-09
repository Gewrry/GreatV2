<?php
// database/migrations/xxxx_create_rpta_rev_yr_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rpta_rev_yr', function (Blueprint $table) {
            $table->id();
            $table->integer('rev_yr');
            $table->timestamps();
        });

        // Insert default record
        DB::table('rpta_rev_yr')->insert([
            'rev_yr' => date('Y'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('rpta_rev_yr');
    }
};