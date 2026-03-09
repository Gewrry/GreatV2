<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change payment_mode from ENUM to a flexible string
        DB::statement("ALTER TABLE rpt_payments MODIFY payment_mode VARCHAR(50) NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        // Revert to original ENUM (note: data with non-enum values will be lost)
        DB::statement("ALTER TABLE rpt_payments MODIFY payment_mode ENUM('cash','check','gcash','others') NOT NULL DEFAULT 'cash'");
    }
};
