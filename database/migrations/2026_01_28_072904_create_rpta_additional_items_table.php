<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rpta_additional_items', function (Blueprint $table) {
            $table->id();
            $table->text('add_name');
            $table->enum('add_q', ['YES', 'NO'])->default('YES');
            $table->decimal('add_unitval', 15, 2)->nullable();
            $table->decimal('add_percent', 8, 2)->nullable();
            $table->text('add_desc')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpta_additional_items');
    }
};