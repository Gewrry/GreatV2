<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_faas_rpta_audit_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('faas_rpta_audit', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('action_taken', 20); // create, update, delete
            $table->json('new_data')->nullable();
            $table->json('old_data')->nullable();
            $table->timestamps();

            $table->index('username');
            $table->index('action_taken');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faas_rpta_audit');
    }
};