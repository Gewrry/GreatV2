<?php
// database/migrations/xxxx_create_bpls_owners_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bpls_owners', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_4ps')->default(false);
            $table->boolean('is_solo_parent')->default(false);
            $table->boolean('is_senior')->default(false);
            $table->boolean('discount_10')->default(false);
            $table->boolean('discount_5')->default(false);
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('municipality')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('emergency_contact_person')->nullable();
            $table->string('emergency_mobile')->nullable();
            $table->string('emergency_email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpls_owners');
    }
};