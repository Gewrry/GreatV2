<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faas_predecessors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faas_property_id')->constrained('faas_properties')->onDelete('cascade');
            $table->foreignId('previous_faas_property_id')->constrained('faas_properties')->onDelete('cascade');
            $table->string('relation_type')->default('subdivision'); // subdivision, consolidation, merge
            $table->timestamps();

            // Prevent duplicate links
            $table->unique(['faas_property_id', 'previous_faas_property_id'], 'unique_predecessor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faas_predecessors');
    }
};
