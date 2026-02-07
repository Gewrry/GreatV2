<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_rpta_other_improvement_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rpta_other_improvement', function (Blueprint $table) {
            $table->id();
            $table->string('kind_name');
            $table->decimal('kind_value', 15, 2);
            $table->date('kind_date');
            $table->timestamps();

            $table->index('kind_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpta_other_improvement');
    }
};