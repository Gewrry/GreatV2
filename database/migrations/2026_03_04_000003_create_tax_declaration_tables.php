<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── TAX DECLARATIONS ──────────────────────────────────────────────────

        Schema::create('tax_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('td_no')->unique()->nullable()->comment('Official TD number, generated on approval');
            $table->string('prev_td_no')->nullable()->comment('Previous TD if this is a revision');
            $table->foreignId('faas_property_id')->constrained('faas_properties')->cascadeOnDelete();
            $table->foreignId('revision_year_id')->nullable()->constrained('rpta_revision_years')->nullOnDelete();

            // Effective period
            $table->year('effectivity_year');
            $table->enum('property_type', ['land', 'building', 'machinery', 'mixed'])->default('land');

            // Summary Values (rolled up from FAAS components)
            $table->decimal('total_market_value', 18, 2)->default(0);
            $table->decimal('total_assessed_value', 18, 2)->default(0);
            $table->boolean('is_taxable')->default(true);
            $table->decimal('tax_rate', 6, 5)->default(0.02)->comment('e.g. 0.02 = 2% basic RPT');

            // Reason for declaration
            $table->enum('declaration_reason', [
                'initial',
                'revision_general',
                'revision_specific',
                'transfer',
                'cancellation',
            ])->default('initial');

            // Workflow
            $table->enum('status', ['draft', 'for_review', 'approved', 'forwarded', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_declarations');
    }
};
