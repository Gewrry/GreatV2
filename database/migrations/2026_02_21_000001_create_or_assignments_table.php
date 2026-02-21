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
            $table->string('start_or');              // e.g. 5234501
            $table->string('end_or');                // e.g. 5234550
            $table->enum('receipt_type', ['51C', 'RPTA', 'CTC']);
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();               // cashier (from users table)
            $table->string('cashier_name');          // denormalized for display speed
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('or_assignments');
    }
};