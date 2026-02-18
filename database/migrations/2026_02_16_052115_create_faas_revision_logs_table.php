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
        Schema::create('faas_revision_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faas_id'); // TD Master ID
            $table->unsignedBigInteger('component_id')->nullable();
            $table->string('component_type')->nullable(); // LAND, BLDG, MACH
            $table->string('revision_type'); // Correction, Characteristic Change, Ownership Transfer
            $table->text('reason')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('encoded_by');
            $table->timestamp('revision_date')->useCurrent();
            $table->timestamps();

            $table->foreign('faas_id')->references('id')->on('faas_gen_rev')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faas_revision_logs');
    }
};
