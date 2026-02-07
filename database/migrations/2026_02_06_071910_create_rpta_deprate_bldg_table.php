<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_rpta_deprate_bldg_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rpta_deprate_bldg', function (Blueprint $table) {
            $table->id();
            $table->text('dep_name');
            $table->decimal('dep_rate', 8, 2);
            $table->text('dep_desc')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpta_deprate_bldg');
    }
};