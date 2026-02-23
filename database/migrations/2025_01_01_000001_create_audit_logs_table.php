<?php
// database/migrations/2025_01_01_000001_create_audit_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 150)->nullable();

            // What was done
            $table->string('module', 100)->index();           // BPLS | RPTA | Settings | Auth …
            $table->string('action', 100)->index();           // created | updated | deleted | payment …
            $table->text('description');

            // Which record was affected
            $table->string('model_type', 255)->nullable()->index();
            $table->unsignedBigInteger('model_id')->nullable()->index();

            // Change tracking (JSON)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Request context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable();

            // Result
            $table->enum('status', ['success', 'failed', 'warning'])->default('success')->index();

            // Anything extra
            $table->json('extra')->nullable();

            $table->timestamps();

            // Composite index for fast model-specific lookups
            $table->index(['model_type', 'model_id']);
            // Composite index for time-based + module queries
            $table->index(['module', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};