<?php
// database/migrations/xxxx_create_rpt_tc_tbl_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rpt_tc_tbl', function (Blueprint $table) {
            $table->id();
            $table->string('tcode', 5);
            $table->string('tcode_desc', 200);
            $table->timestamps();

            $table->unique('tcode'); // Ensure unique transaction codes
        });
    }

    public function down()
    {
        Schema::dropIfExists('rpt_tc_tbl');
    }
};