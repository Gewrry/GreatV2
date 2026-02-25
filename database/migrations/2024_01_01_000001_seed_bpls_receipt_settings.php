<?php
// database/migrations/2024_01_01_000001_seed_bpls_receipt_settings.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Seeds default receipt customization settings into bpls_settings table.
     */
    public function up(): void
    {
        // Ensure the bpls_settings table has a 'group' column (add if not present)
        if (!Schema::hasColumn('bpls_settings', 'group')) {
            Schema::table('bpls_settings', function (Blueprint $table) {
                $table->string('group')->default('general')->after('label');
            });
        }

        $receiptSettings = [
            // ── Header ───────────────────────────────────────────────────────
            [
                'key' => 'receipt_header_line1',
                'value' => 'Official Receipt of the Republic of the Philippines',
                'label' => 'Receipt Header Line 1',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_office_name',
                'value' => 'Office of the Treasurer',
                'label' => 'Office Name (Main Title)',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_header_line3',
                'value' => 'Province of Laguna',
                'label' => 'Receipt Header Line 3 (Province/Location)',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_agency_name',
                'value' => 'MTO-Majayjay',
                'label' => 'Agency Name (e.g. MTO-Majayjay)',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_af_label',
                'value' => 'Accountable form No. 51',
                'label' => 'Accountable Form Label',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_received_text',
                'value' => 'Received the amount stated above',
                'label' => 'Received Text (above signatories)',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_footer_note',
                'value' => '',
                'label' => 'Footer Note (optional)',
                'group' => 'receipt',
            ],

            // ── Signatory 1 (always shown) ────────────────────────────────
            [
                'key' => 'receipt_signatory1_name',
                'value' => '',
                'label' => 'Signatory 1 Name (leave blank to use logged-in cashier)',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_signatory1_title',
                'value' => 'Cashier Officer',
                'label' => 'Signatory 1 Title',
                'group' => 'receipt',
            ],

            // ── Signatory 2 (optional) ────────────────────────────────────
            [
                'key' => 'receipt_signatory2_enabled',
                'value' => '0',
                'label' => 'Enable Signatory 2',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_signatory2_name',
                'value' => '',
                'label' => 'Signatory 2 Name',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_signatory2_title',
                'value' => '',
                'label' => 'Signatory 2 Title',
                'group' => 'receipt',
            ],

            // ── Signatory 3 (optional) ────────────────────────────────────
            [
                'key' => 'receipt_signatory3_enabled',
                'value' => '0',
                'label' => 'Enable Signatory 3',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_signatory3_name',
                'value' => '',
                'label' => 'Signatory 3 Name',
                'group' => 'receipt',
            ],
            [
                'key' => 'receipt_signatory3_title',
                'value' => '',
                'label' => 'Signatory 3 Title',
                'group' => 'receipt',
            ],
        ];

        foreach ($receiptSettings as $setting) {
            DB::table('bpls_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'receipt_header_line1',
            'receipt_office_name',
            'receipt_header_line3',
            'receipt_agency_name',
            'receipt_af_label',
            'receipt_received_text',
            'receipt_footer_note',
            'receipt_signatory1_name',
            'receipt_signatory1_title',
            'receipt_signatory2_enabled',
            'receipt_signatory2_name',
            'receipt_signatory2_title',
            'receipt_signatory3_enabled',
            'receipt_signatory3_name',
            'receipt_signatory3_title',
        ];

        DB::table('bpls_settings')->whereIn('key', $keys)->delete();
    }
};