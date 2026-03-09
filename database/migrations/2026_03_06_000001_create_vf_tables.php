<?php
// database/migrations/2026_03_06_000001_create_vf_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // vf_todas already exists — skip it
        // Run: DESCRIBE vf_todas; in phpMyAdmin to confirm column names

        // =====================================================================
        // VF_OWNERS
        // =====================================================================
        if (!Schema::hasTable('vf_owners')) {
            Schema::create('vf_owners', function (Blueprint $table) {
                $table->id();
                $table->string('last_name');
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('citizenship')->default('FILIPINO');
                $table->enum('civil_status', ['single', 'married', 'widowed', 'separated'])->nullable();
                $table->enum('gender', ['male', 'female'])->nullable();
                $table->string('ownership_type')->nullable();
                $table->string('contact_number')->nullable();
                $table->date('birthday')->nullable();
                $table->string('barangay')->nullable();
                $table->text('current_address')->nullable();
                $table->string('ctc_receipt_number')->nullable();
                $table->date('ctc_date_issued')->nullable();
                $table->string('ctc_issued_at')->nullable()->default('MTO-Majayjay');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // =====================================================================
        // VF_FRANCHISES
        // =====================================================================
        if (!Schema::hasTable('vf_franchises')) {
            Schema::create('vf_franchises', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('fn_number')->unique();
                $table->string('permit_number')->unique();
                $table->date('permit_date');
                $table->enum('permit_type', ['new', 'renewal', 'transfer', 'amendment'])->default('new');
                $table->foreignId('owner_id')->constrained('vf_owners')->cascadeOnDelete();
                $table->foreignId('toda_id')->nullable()->constrained('vf_todas')->nullOnDelete();
                $table->string('driver_name')->nullable();
                $table->string('driver_contact')->nullable();
                $table->string('license_number')->nullable();
                $table->text('remarks')->nullable();
                $table->enum('status', ['active', 'pending', 'expired', 'cancelled'])->default('pending');
                $table->foreignId('encoded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // =====================================================================
        // VF_VEHICLES
        // =====================================================================
        if (!Schema::hasTable('vf_vehicles')) {
            Schema::create('vf_vehicles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained('vf_franchises')->cascadeOnDelete();
                $table->string('make')->nullable();
                $table->string('model')->nullable();
                $table->string('franchise_type')->nullable();
                $table->string('motor_number')->nullable();
                $table->string('chassis_number')->nullable();
                $table->string('plate_number')->nullable();
                $table->unsignedSmallInteger('year_model')->nullable();
                $table->string('color')->nullable();
                $table->string('sticker_number')->nullable();
                $table->timestamps();
            });
        }

        // =====================================================================
        // VF_FRANCHISE_HISTORY
        // =====================================================================
        if (!Schema::hasTable('vf_franchise_history')) {
            Schema::create('vf_franchise_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained('vf_franchises')->cascadeOnDelete();
                $table->enum('action', ['created', 'renewed', 'transferred', 'amended', 'cancelled']);
                $table->string('permit_number')->nullable();
                $table->date('action_date');
                $table->text('notes')->nullable();
                $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vf_franchise_history');
        Schema::dropIfExists('vf_vehicles');
        Schema::dropIfExists('vf_franchises');
        Schema::dropIfExists('vf_owners');
    }
};