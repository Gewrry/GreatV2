<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('division_name');
            $table->string('division_code', 20)->unique();
            $table->text('division_description')->nullable();
            $table->unsignedBigInteger('office_id');
            $table->string('division_head', 100)->nullable();
            $table->integer('order_sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onDelete('cascade');

            $table->index('office_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
