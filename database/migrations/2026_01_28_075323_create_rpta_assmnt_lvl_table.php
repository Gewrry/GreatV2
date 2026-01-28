<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rpta_assmnt_lvl', function (Blueprint $table) {
            $table->id();
            $table->string('assmnt_name', 50);
            $table->decimal('assmnt_from', 15, 2)->nullable();
            $table->decimal('assmnt_to', 15, 2)->nullable();
            $table->decimal('assmnt_percent', 8, 2);
            $table->enum('assmnt_cat', ['LAND', 'BUILDING', 'MACHINE']);
            $table->enum('assmnt_kind', [
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
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpta_assmnt_lvl');
    }
};