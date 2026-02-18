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
        Schema::create('faas_lands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faas_id');
            $table->string('td_no')->nullable();
            $table->string('pin')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('survey_no')->nullable();
            $table->string('zoning')->nullable();
            $table->string('is_corner')->nullable();
            $table->string('road_type')->nullable();
            $table->string('location_class')->nullable();
            $table->decimal('area', 15, 4)->default(0);
            $table->string('assmt_kind')->nullable();
            $table->string('actual_use')->nullable();
            $table->decimal('unit_value', 15, 2)->default(0);
            $table->decimal('adjustment_factor', 5, 2)->default(0);
            $table->decimal('assessment_level', 5, 2)->default(0);
            $table->decimal('market_value', 15, 2)->default(0);
            $table->decimal('assessed_value', 15, 2)->default(0);
            $table->date('effectivity_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('faas_id')->references('id')->on('faas_gen_rev')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faas_lands');
    }
};
