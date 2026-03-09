<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bpls_business_amendments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_entry_id')
                ->constrained('bpls_business_entries')
                ->onDelete('cascade');

            // Snapshot of OLD values (before amendment)
            $table->string('old_business_name')->nullable();
            $table->string('old_trade_name')->nullable();
            $table->string('old_tin_no')->nullable();
            $table->string('old_type_of_business')->nullable();
            $table->string('old_business_nature')->nullable();
            $table->string('old_business_scale')->nullable();
            $table->string('old_business_barangay')->nullable();
            $table->string('old_business_municipality')->nullable();
            $table->string('old_business_street')->nullable();
            $table->string('old_last_name')->nullable();
            $table->string('old_first_name')->nullable();
            $table->string('old_middle_name')->nullable();
            $table->string('old_mobile_no')->nullable();
            $table->string('old_email')->nullable();
            $table->string('old_business_mobile')->nullable();
            $table->string('old_business_email')->nullable();
            $table->string('old_business_organization')->nullable();
            $table->string('old_zone')->nullable();
            $table->integer('old_total_employees')->nullable();

            // Snapshot of NEW values
            $table->string('new_business_name')->nullable();
            $table->string('new_trade_name')->nullable();
            $table->string('new_tin_no')->nullable();
            $table->string('new_type_of_business')->nullable();
            $table->string('new_business_nature')->nullable();
            $table->string('new_business_scale')->nullable();
            $table->string('new_business_barangay')->nullable();
            $table->string('new_business_municipality')->nullable();
            $table->string('new_business_street')->nullable();
            $table->string('new_last_name')->nullable();
            $table->string('new_first_name')->nullable();
            $table->string('new_middle_name')->nullable();
            $table->string('new_mobile_no')->nullable();
            $table->string('new_email')->nullable();
            $table->string('new_business_mobile')->nullable();
            $table->string('new_business_email')->nullable();
            $table->string('new_business_organization')->nullable();
            $table->string('new_zone')->nullable();
            $table->integer('new_total_employees')->nullable();

            // Fields that actually changed (JSON array of field names)
            $table->json('changed_fields')->nullable();

            // Amendment metadata
            $table->string('amendment_type')->default('edit'); // edit, rename, address_change, owner_change
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->string('amended_by_name')->nullable(); // clerk name snapshot
            $table->foreignId('amended_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('amended_at')->nullable();

            $table->timestamps();

            $table->index('business_entry_id');
            $table->index('amendment_type');
            $table->index('amended_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpls_business_amendments');
    }
};