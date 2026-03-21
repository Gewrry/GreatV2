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
        Schema::create('rpt_registration_owners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rpt_property_registration_id');
            $table->string('owner_name');
            $table->string('owner_tin')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('owner_contact')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('rpt_property_registration_id', 'fk_reg_on_reg')
                  ->references('id')
                  ->on('rpt_property_registrations')
                  ->onDelete('cascade');
        });

        Schema::create('faas_owners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faas_property_id');
            $table->string('owner_name');
            $table->string('owner_tin')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('owner_contact')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('faas_property_id', 'fk_faas_on_prop')
                  ->references('id')
                  ->on('faas_properties')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faas_owners');
        Schema::dropIfExists('rpt_registration_owners');
    }
};
