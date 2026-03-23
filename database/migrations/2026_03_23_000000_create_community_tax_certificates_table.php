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
        Schema::create('community_tax_certificates', function (Blueprint $table) {
            $table->id();
            
            // HEADER
            $table->string('ctc_number', 50)->unique();
            $table->integer('year');
            $table->string('place_of_issue', 255);
            $table->date('date_issued');
            
            // TAXPAYER PERSONAL INFO
            $table->string('surname', 100);
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('tin', 20)->nullable();
            $table->string('address', 255);
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('restrict');
            $table->string('barangay_name', 100);
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->string('citizenship', 50)->default('FILIPINO');
            $table->string('icr_number', 50)->nullable()->comment('For aliens only');
            $table->string('place_of_birth', 255)->nullable();
            $table->decimal('height', 5, 1)->nullable()->comment('In centimeters');
            $table->enum('civil_status', ['SINGLE', 'MARRIED', 'WIDOWED', 'LEGALLY_SEPARATED']);
            $table->date('date_of_birth');
            $table->decimal('weight', 5, 1)->nullable()->comment('In kg');
            $table->string('profession', 150)->nullable();
            
            // TAX COMPUTATION
            $table->decimal('basic_tax', 10, 2)->default(5.00);
            $table->decimal('gross_receipts_business', 15, 2)->default(0);
            $table->decimal('gross_receipts_business_tax', 10, 2)->default(0);
            $table->decimal('salary_income', 15, 2)->default(0);
            $table->integer('salary_months')->default(0);
            $table->decimal('salary_tax', 10, 2)->default(0);
            $table->decimal('real_property_income', 15, 2)->default(0);
            $table->decimal('real_property_tax', 10, 2)->default(0);
            $table->decimal('additional_tax', 10, 2)->default(0);
            $table->decimal('interest_percent', 5, 2)->default(0);
            $table->decimal('interest_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            $table->timestamps();
            
            // Indexes
            $table->index('year');
            $table->index('surname', 'idx_surname');
            $table->index('first_name', 'idx_first_name');
            $table->index('date_issued');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_tax_certificates');
    }
};
