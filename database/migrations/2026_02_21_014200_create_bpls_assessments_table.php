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
        Schema::create('bpls_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpls_application_id')->constrained()->cascadeOnDelete();
            $table->decimal('capital_investment', 15, 2)->default(0);
            $table->decimal('business_tax', 12, 2)->default(0);
            $table->decimal('mayors_permit_fee', 12, 2)->default(0);
            $table->decimal('sanitary_fee', 12, 2)->default(0);
            $table->decimal('fire_inspection_fee', 12, 2)->default(0);
            $table->decimal('zoning_fee', 12, 2)->default(0);
            $table->decimal('garbage_fee', 12, 2)->default(0);
            $table->decimal('surcharge', 12, 2)->default(0);
            $table->decimal('penalty', 12, 2)->default(0);
            $table->decimal('total_due', 12, 2)->default(0); // matches BusinessEntry.total_due
            $table->enum('mode_of_payment', ['full', 'quarterly'])->default('full');
            $table->text('notes')->nullable();
            $table->foreignId('assessed_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpls_assessments');
    }
};
