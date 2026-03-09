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
        Schema::create('bpls_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique(); // APP-2026-00001

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            // Links to your existing tables from BusinessEntriesController
            $table->foreignId('bpls_business_id')->nullable()->constrained('bpls_businesses')->nullOnDelete();
            $table->foreignId('bpls_owner_id')->nullable()->constrained('bpls_owners')->nullOnDelete();
            $table->foreignId('business_entry_id')->nullable()->constrained('bpls_business_entries')->nullOnDelete();

            $table->enum('application_type', ['new', 'renewal', 'amendment'])->default('new');
            $table->integer('permit_year')->default(2026);

            // ← THE 6-STAGE STATE MACHINE (matches your BusinessEntry statuses)
            $table->enum('workflow_status', [
                'draft',        // Stage 1: Client filling form
                'submitted',    // Stage 2: Client uploaded documents
                'verification', // Stage 3: BPLO checking docs
                'assessment',   // Stage 4: Treasurer computing fees
                'payment',      // Stage 5: Client paying
                'approved',     // Stage 6: Permit released
                'rejected',
                'returned',     // Returned to client for correction
            ])->default('draft');

            // Workflow timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            // Back-office handlers
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpls_applications');
    }
};
