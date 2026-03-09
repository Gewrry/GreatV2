<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. Benefits master table ──────────────────────────────────────
        Schema::create('bpls_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // e.g. "PWD", "4PS"
            $table->string('label');                    // Display: "Persons with Disability"
            $table->string('field_key')->unique();      // Unique key: "is_pwd", "is_4ps" – used in code & pivot
            $table->decimal('discount_percent', 5, 2)->default(0); // e.g. 20.00 for 20%
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── 2. Pivot: owner ↔ benefit ─────────────────────────────────────
        Schema::create('bpls_owner_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('bpls_owners')->cascadeOnDelete();
            $table->foreignId('benefit_id')->constrained('bpls_benefits')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['owner_id', 'benefit_id']);
        });

        // ── 3. Pivot: business_entry ↔ benefit (snapshot at time of entry) ─
        Schema::create('bpls_entry_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_entry_id')
                ->constrained('bpls_business_entries')
                ->cascadeOnDelete();
            $table->foreignId('benefit_id')->constrained('bpls_benefits')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['business_entry_id', 'benefit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpls_entry_benefits');
        Schema::dropIfExists('bpls_owner_benefits');
        Schema::dropIfExists('bpls_benefits');
    }
};