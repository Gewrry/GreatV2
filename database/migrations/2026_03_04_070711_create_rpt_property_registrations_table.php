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
        Schema::create('rpt_property_registrations', function (Blueprint $table) {
            $table->id();
            
            // Section A: Owner Information
            $table->string('owner_name');
            $table->string('owner_tin')->nullable();
            $table->string('owner_address');
            $table->string('owner_contact')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('administrator_name')->nullable();
            $table->string('administrator_address')->nullable();
            
            // Section B: Property Identification
            $table->enum('property_type', ['land', 'building', 'machinery', 'mixed']);
            $table->foreignId('barangay_id')->constrained('barangays');
            $table->string('street')->nullable();
            $table->string('municipality');
            $table->string('province');
            $table->string('title_no')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('blk_no')->nullable();
            $table->string('survey_no')->nullable();
            
            // Section C: Basic Details (No Valuation)
            $table->decimal('estimated_floor_area', 12, 4)->nullable();
            $table->text('machinery_description')->nullable();
            
            // System Details
            $table->enum('status', ['registered', 'archived'])->default('registered');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpt_property_registrations');
    }
};
