<?php
// database/migrations/2026_03_05_000001_create_form_options_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_options', function (Blueprint $table) {
            $table->id();
            $table->string('category', 60)->index();   // e.g. 'type_of_business'
            $table->string('value', 255);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['category', 'value']);
        });

        // ── Seed default options ──────────────────────────────────────────
        $defaults = [
            'type_of_business' => [
                'Retail',
                'Wholesale',
                'Manufacturing',
                'Service',
                'Food & Beverage',
                'Construction',
                'Transportation',
                'Other',
            ],
            'business_organization' => [
                'Sole Proprietorship',
                'Partnership',
                'Corporation',
                'Cooperative',
                'One Person Corporation (OPC)',
            ],
            'business_area_type' => [
                'Owned',
                'Leased',
                'Rented',
                'Government-owned',
            ],
            'business_scale' => [
                'Micro (Assets up to P3M)',
                'Small (P3M - P15M)',
                'Medium (P15M - P100M)',
                'Large (Above P100M)',
            ],
            'business_sector' => [
                'Agriculture',
                'Industry',
                'Services',
                'Tourism',
                'Health',
                'Education',
                'IT/BPO',
                'Finance',
            ],
            'zone' => [
                'Zone 1 - Commercial',
                'Zone 2 - Industrial',
                'Zone 3 - Residential',
                'Zone 4 - Mixed Use',
                'Zone 5 - Agricultural',
                'Special Economic Zone',
            ],
            'occupancy' => [
                'Ground Floor',
                '2nd Floor',
                '3rd Floor',
                'Basement',
                'Multi-level',
                'Entire Building',
            ],
            'amendment_from' => [
                'Sole Proprietorship',
                'Partnership',
                'Corporation',
                'Cooperative',
                'One Person Corporation (OPC)',
            ],
            'amendment_to' => [
                'Sole Proprietorship',
                'Partnership',
                'Corporation',
                'Cooperative',
                'One Person Corporation (OPC)',
            ],
        ];

        $rows = [];
        $now = now();

        foreach ($defaults as $category => $values) {
            foreach ($values as $i => $value) {
                $rows[] = [
                    'category' => $category,
                    'value' => $value,
                    'sort_order' => $i,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('form_options')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('form_options');
    }
};