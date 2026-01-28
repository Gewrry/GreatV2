<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_rpt_au_value_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rpt_au_value', function (Blueprint $table) {
            $table->id();
            $table->string('actual_use', 50);
            $table->string('class_struc', 10);
            $table->decimal('unit_value', 15, 2);
            $table->enum('au_cat', ['LAND', 'BUILDING', 'MACHINE']);
            $table->enum('assmt_kind', [
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
            $table->year('rev_date');
            $table->timestamps();

            $table->index(['au_cat', 'assmt_kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpt_au_value');
    }
};