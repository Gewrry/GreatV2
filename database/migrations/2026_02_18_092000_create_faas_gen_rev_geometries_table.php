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
        Schema::create('faas_gen_rev_geometries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faas_id');
            $table->string('pin', 100)->nullable();
            $table->longText('geometry')->comment('GeoJSON Polygon/MultiPolygon data');
            $table->string('fill_color', 20)->default('#4F46E5');
            $table->timestamps();

            $table->foreign('faas_id')->references('id')->on('faas_gen_rev')->onDelete('cascade');
            $table->index('pin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faas_gen_rev_geometries');
    }
};
