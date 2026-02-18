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
        Schema::create('faas_owners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faas_id');
            $table->unsignedBigInteger('owner_id');
            $table->timestamps();

            $table->foreign('faas_id')->references('id')->on('faas_gen_rev')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('faas_rpta_owner_select')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faas_owners');
    }
};
