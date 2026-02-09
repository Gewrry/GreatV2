<?php
// database/migrations/xxxx_create_faas_gen_rev_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('faas_gen_rev', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20); // land, building, machine
            $table->integer('revised_year');
            $table->integer('gen_rev');
            $table->string('bcode', 10);
            $table->string('rev_unit_val', 50);
            $table->text('gen_desc')->nullable();
            $table->string('statt', 20)->default('revised');
            $table->string('encoded_by');
            $table->date('entry_date')->nullable();
            $table->string('entry_by')->nullable();
            $table->timestamp('encoded_date')->useCurrent();
            $table->timestamps();

            $table->index(['kind', 'revised_year', 'bcode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('faas_gen_rev');
    }
};