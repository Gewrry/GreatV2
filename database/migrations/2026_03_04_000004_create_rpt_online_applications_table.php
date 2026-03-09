<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── ONLINE PROPERTY REGISTRATION ─────────────────────────────────────

        Schema::create('rpt_online_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique()->comment('Public-facing tracking number');

            // Client submitting the application (nullable for guest)
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();

            // Owner / Applicant details
            $table->string('owner_name');
            $table->string('owner_tin')->nullable();
            $table->string('owner_address');
            $table->string('owner_contact')->nullable();
            $table->string('owner_email')->nullable();

            // Property location
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
            $table->string('street')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();

            // Property details (basic info from the applicant)
            $table->enum('property_type', ['land', 'building', 'machinery', 'mixed'])->default('land');
            $table->string('lot_no')->nullable();
            $table->string('blk_no')->nullable();
            $table->string('survey_no')->nullable();
            $table->string('title_no')->nullable();
            $table->decimal('land_area', 14, 4)->nullable();
            $table->text('property_description')->nullable();

            // Staff review
            $table->enum('status', [
                'pending',
                'under_review',
                'for_inspection',
                'approved',
                'returned',
                'rejected',
            ])->default('pending');
            $table->text('staff_remarks')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            // If approved, link to the generated FAAS
            $table->foreignId('faas_property_id')->nullable()->constrained('faas_properties')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Document uploads submitted together with the online application
        Schema::create('rpt_application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpt_online_application_id')->constrained('rpt_online_applications')->cascadeOnDelete();
            $table->enum('type', [
                'title_deed',
                'tax_clearance',
                'deed_of_sale',
                'sketch_plan',
                'special_power_of_attorney',
                'others',
            ])->default('others');
            $table->string('label')->nullable()->comment('Friendly name from the applicant');
            $table->string('file_path');
            $table->string('original_filename');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpt_application_documents');
        Schema::dropIfExists('rpt_online_applications');
    }
};
