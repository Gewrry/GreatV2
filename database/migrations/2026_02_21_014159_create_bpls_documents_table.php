<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bpls_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpls_application_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', [
                'dti_sec_cda',
                'barangay_clearance',
                'lease_contract',
                'owners_consent',
                'community_tax_certificate',
                'fire_clearance',
                'sanitary_permit',
                'others',
            ]);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpls_documents');
    }
};
