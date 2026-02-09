<?php
// database/migrations/xxxx_create_defaultz_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::create('defaultz', function (Blueprint $table) {
            $table->id();
            $table->string('mun_assessor')->nullable();
            $table->string('mun_ass_designation')->nullable();
            $table->timestamps();
        });

        // Insert default records
        DB::table('defaultz')->insert([
            ['mun_assessor' => 'Municipal Assessor', 'mun_ass_designation' => 'Municipal Assessor', 'created_at' => now(), 'updated_at' => now()],
            ['mun_assessor' => 'Provincial Assessor', 'mun_ass_designation' => 'Provincial Assessor', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('defaultz');
    }
};