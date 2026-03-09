<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bpls_business_entries', function (Blueprint $table) {
            $table->id();

            // Owner Info
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();

            // Legal Entity
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_4ps')->default(false);
            $table->boolean('is_solo_parent')->default(false);
            $table->boolean('is_senior')->default(false);
            $table->boolean('discount_10')->default(false);
            $table->boolean('discount_5')->default(false);

            // Owner Address
            $table->string('owner_region')->nullable();
            $table->string('owner_province')->nullable();
            $table->string('owner_municipality')->nullable();
            $table->string('owner_barangay')->nullable();
            $table->string('owner_street')->nullable();

            // Business Details
            $table->string('business_name');
            $table->string('trade_name')->nullable();
            $table->date('date_of_application')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('dti_sec_cda_no')->nullable();
            $table->date('dti_sec_cda_date')->nullable();
            $table->string('business_mobile')->nullable();
            $table->string('business_email')->nullable();
            $table->string('type_of_business')->nullable();
            $table->string('amendment_from')->nullable();
            $table->string('amendment_to')->nullable();
            $table->boolean('tax_incentive')->default(false);
            $table->string('business_organization')->nullable();
            $table->string('business_area_type')->nullable();
            $table->string('business_scale')->nullable();
            $table->string('business_sector')->nullable();
            $table->string('zone')->nullable();
            $table->string('occupancy')->nullable();
            $table->decimal('business_area_sqm', 10, 2)->nullable();
            $table->integer('total_employees')->nullable();
            $table->integer('employees_lgu')->nullable();

            // Business Address
            $table->string('business_region')->nullable();
            $table->string('business_province')->nullable();
            $table->string('business_municipality')->nullable();
            $table->string('business_barangay')->nullable();
            $table->string('business_street')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_person')->nullable();
            $table->string('emergency_mobile')->nullable();
            $table->string('emergency_email')->nullable();

            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpls_business_entries');
    }
};