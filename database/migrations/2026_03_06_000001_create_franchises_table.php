<?php
// database/migrations/2026_03_06_000001_create_franchises_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // =====================================================================
        // TODAS  (Transport Organization / Driver's Association)
        // =====================================================================
        Schema::create('vf_todas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->string('barangay')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =====================================================================
        // FRANCHISE OWNERS / APPLICANTS
        // =====================================================================
        Schema::create('vf_owners', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('citizenship')->default('FILIPINO');
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated'])->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('ownership_type')->nullable();   // private, for_hire, government
            $table->string('contact_number')->nullable();
            $table->date('birthday')->nullable();
            $table->string('barangay')->nullable();
            $table->text('current_address')->nullable();

            // Community Tax Certificate
            $table->string('ctc_receipt_number')->nullable();
            $table->date('ctc_date_issued')->nullable();
            $table->string('ctc_issued_at')->nullable()->default('MTO-Majayjay');

            $table->timestamps();
            $table->softDeletes();
        });

        // =====================================================================
        // FRANCHISES  (main record)
        // =====================================================================
        Schema::create('vf_franchises', function (Blueprint $table) {
            $table->id();

            // Auto-incremented franchise number (FN#)
            $table->unsignedInteger('fn_number')->unique();

            // Permit info
            $table->string('permit_number')->unique();
            $table->date('permit_date');
            $table->enum('permit_type', ['new', 'renewal', 'transfer', 'amendment'])->default('new');

            // Relationships
            $table->foreignId('owner_id')->constrained('vf_owners')->cascadeOnDelete();
            $table->foreignId('toda_id')->nullable()->constrained('vf_todas')->nullOnDelete();

            // Driver info (can differ from owner)
            $table->string('driver_name')->nullable();
            $table->string('driver_contact')->nullable();
            $table->string('license_number')->nullable();

            // Remarks / notes
            $table->text('remarks')->nullable();

            // Status
            $table->enum('status', ['active', 'pending', 'expired', 'cancelled'])->default('pending');

            // Encoded by
            $table->foreignId('encoded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // =====================================================================
        // VEHICLES  (a franchise may have one vehicle)
        // =====================================================================
        Schema::create('vf_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('vf_franchises')->cascadeOnDelete();

            $table->string('make')->nullable();            // Honda, Yamaha, etc.
            $table->string('model')->nullable();           // XRM 125
            $table->string('franchise_type')->nullable();  // tricycle, kuliglig, etc.
            $table->string('motor_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('plate_number')->nullable();
            $table->year('year_model')->nullable();
            $table->string('color')->nullable();
            $table->string('sticker_number')->nullable();

            $table->timestamps();
        });

        // =====================================================================
        // FRANCHISE HISTORY  (track renewals, transfers, amendments)
        // =====================================================================
        Schema::create('vf_franchise_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('vf_franchises')->cascadeOnDelete();
            $table->enum('action', ['created', 'renewed', 'transferred', 'amended', 'cancelled']);
            $table->string('permit_number')->nullable();   // new permit number on renewal
            $table->date('action_date');
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vf_franchise_history');
        Schema::dropIfExists('vf_vehicles');
        Schema::dropIfExists('vf_franchises');
        Schema::dropIfExists('vf_owners');
        Schema::dropIfExists('vf_todas');
    }
};