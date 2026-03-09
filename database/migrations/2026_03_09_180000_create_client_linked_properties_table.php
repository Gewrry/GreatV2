<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_linked_properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('tax_declaration_id');
            $table->string('nickname')->nullable();
            $table->timestamp('linked_at')->useCurrent();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            $table->foreign('tax_declaration_id')->references('id')->on('tax_declarations')->cascadeOnDelete();
            $table->unique(['client_id', 'tax_declaration_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_linked_properties');
    }
};
