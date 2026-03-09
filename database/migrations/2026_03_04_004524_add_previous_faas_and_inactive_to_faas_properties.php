<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            // Governance Check #6: General Revision — link new FAAS to the old one it supersedes
            $table->unsignedBigInteger('previous_faas_property_id')
                  ->nullable()
                  ->after('remarks')
                  ->comment('For General Revision: the superseded FAAS record');

            $table->foreign('previous_faas_property_id')
                  ->references('id')
                  ->on('faas_properties')
                  ->nullOnDelete();
        });

        // Governance Check #5: Immutable TD Activity Log table for full audit trail
        Schema::create('td_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_declaration_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');       // created, submitted_review, approved, forwarded, cancelled
            $table->text('description')->nullable();
            $table->json('meta')->nullable(); // snapshot of AV, tax_rate, status at time of action
            $table->timestamp('created_at')->useCurrent();
            // Deliberately NO updated_at — logs are append-only and immutable

            $table->foreign('tax_declaration_id')->references('id')->on('tax_declarations')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index('tax_declaration_id');
        });
    }

    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropForeign(['previous_faas_property_id']);
            $table->dropColumn('previous_faas_property_id');
        });
        Schema::dropIfExists('td_activity_logs');
    }
};
