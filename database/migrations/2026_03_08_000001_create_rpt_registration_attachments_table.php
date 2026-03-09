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
        Schema::create('rpt_registration_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpt_property_registration_id')
                  ->constrained('rpt_property_registrations', 'id', 'fk_rpt_reg_attach')
                  ->onDelete('cascade');
            $table->string('type');
            $table->string('label')->nullable();
            $table->string('file_path');
            $table->string('original_filename');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpt_registration_attachments');
    }
};
