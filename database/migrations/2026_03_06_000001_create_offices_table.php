<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('office_name');
            $table->string('office_code', 20)->unique();
            $table->string('office_short_name', 50)->nullable();
            $table->text('office_description')->nullable();
            $table->unsignedBigInteger('parent_office_id')->nullable();
            $table->string('office_head', 100)->nullable();
            $table->string('office_location', 255)->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->integer('order_sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_office_id')
                ->references('id')
                ->on('offices')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
