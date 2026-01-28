<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_rpt_au_tbl_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rpt_au_tbl', function (Blueprint $table) {
            $table->id();
            $table->string('actual_use', 50);
            $table->enum('au_cat', [
                'RESIDENTIAL',
                'AGRICULTURAL',
                'COMMERCIAL',
                'INDUSTRIAL',
                'MINERAL',
                'TIMBERLAND',
                'SPECIAL',
                'GOVERNMENT',
                'RELIGIOUS',
                'CHARITABLE',
                'EDUCATIONAL',
                'OTHERS',
                'ACI'
            ]);
            $table->string('au_desc', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpt_au_tbl');
    }
};