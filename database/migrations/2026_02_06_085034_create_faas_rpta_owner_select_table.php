<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_faas_rpta_owner_select_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('faas_rpta_owner_select', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->text('owner_address')->nullable();
            $table->string('owner_tel', 50)->nullable();
            $table->string('owner_tin', 20)->nullable();
            $table->string('encoded_by');
            $table->timestamps();

            $table->index('owner_name');
            $table->index('encoded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faas_rpta_owner_select');
    }
};