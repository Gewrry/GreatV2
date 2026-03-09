<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('interviewer_id')->nullable();
            $table->string('interview_type', 50)->default('initial');
            $table->dateTime('scheduled_at');
            $table->string('location', 255)->nullable();
            $table->text('notes')->nullable();
            $table->enum('result', ['pending', 'passed', 'failed', 'rescheduled', 'cancelled'])->default('pending');
            $table->decimal('rating', 3, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('conducted_at')->nullable();
            $table->timestamps();

            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->foreign('interviewer_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['applicant_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
