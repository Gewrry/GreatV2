<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employment_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name');
            $table->string('type_code', 20)->unique();
            $table->text('type_description')->nullable();
            $table->string('category', 50)->nullable();
            $table->boolean('is_permanent')->default(false);
            $table->boolean('has_plantilla')->default(false);
            $table->integer('leave_credits_per_year')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_types');
    }
};
