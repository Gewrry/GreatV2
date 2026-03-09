<?php
// database/migrations/xxxx_create_bpls_businesses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bpls_businesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
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
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('municipality')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpls_businesses');
    }
};