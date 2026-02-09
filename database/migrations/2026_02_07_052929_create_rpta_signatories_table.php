<?php
// database/migrations/xxxx_create_rpta_signatories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rpta_signatories', function (Blueprint $table) {
            $table->id();
            $table->string('sign_name');
            $table->string('sign_name_ext');
            $table->date('sign_assign');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rpta_signatories');
    }
};