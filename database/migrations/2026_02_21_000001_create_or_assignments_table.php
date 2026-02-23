<?php
// database/migrations/2026_02_21_000001_create_or_assignments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('or_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('start_or', 20);
            $table->string('end_or', 20);
            $table->string('receipt_type', 10); // 51C, RPTA, CTC
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('cashier_name', 255); // denormalized for display
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('or_assignments');
    }
};