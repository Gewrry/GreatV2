<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vf_collection_natures', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // e.g. "Franchise Fee"
            $table->string('account_code')->nullable(); // e.g. "1-01-01"
            $table->decimal('default_amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vf_collection_natures');
    }
};