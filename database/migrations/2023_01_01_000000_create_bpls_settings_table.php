<?php
// database/migrations/xxxx_create_bpls_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bpls_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();   // e.g. 'advance_discount_annual'
            $table->string('value');            // e.g. '10.00'
            $table->string('label')->nullable(); // Human-readable label
            $table->string('group')->default('advance_discount'); // setting group
            $table->timestamps();
        });

        // Seed default discount values
        $defaults = [
            // Annual payment — paid before Jan 20
            [
                'key' => 'advance_discount_annual',
                'value' => '20.00',
                'label' => 'Annual Payment Discount (%)',
                'group' => 'advance_discount',
            ],
            // Semi-annual — paid before due date
            [
                'key' => 'advance_discount_semi_annual',
                'value' => '10.00',
                'label' => 'Semi-Annual Payment Discount (%)',
                'group' => 'advance_discount',
            ],
            // Quarterly — paid before due date
            [
                'key' => 'advance_discount_quarterly',
                'value' => '5.00',
                'label' => 'Quarterly Payment Discount (%)',
                'group' => 'advance_discount',
            ],
            // Toggle — enable/disable advance discount feature
            [
                'key' => 'advance_discount_enabled',
                'value' => '1',
                'label' => 'Enable Advance Payment Discount',
                'group' => 'advance_discount',
            ],
            // Deadline — how many days before due date qualifies as "advance"
            [
                'key' => 'advance_discount_days_before',
                'value' => '10',
                'label' => 'Days Before Due Date to Qualify as Advance',
                'group' => 'advance_discount',
            ],
        ];

        foreach ($defaults as $setting) {
            DB::table('bpls_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bpls_settings');
    }
};