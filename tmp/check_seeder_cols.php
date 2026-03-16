<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$checks = [
    'faas_properties' => ['property_type', 'owner_name', 'owner_address', 'barangay_id', 'status', 'arp_no', 'pin', 'polygon_coordinates'],
    'faas_lands' => ['faas_property_id', 'survey_no', 'area_sqm', 'rpta_actual_use_id', 'unit_value', 'base_market_value', 'market_value', 'assessment_level', 'assessed_value'],
    'tax_declarations' => ['faas_property_id', 'faas_land_id', 'td_no', 'revision_year_id', 'total_market_value', 'total_assessed_value', 'property_type', 'property_kind', 'effectivity_quarter', 'is_taxable', 'tax_rate', 'status', 'effectivity_year', 'created_by', 'approved_by', 'approved_at'],
    'rpt_billings' => ['tax_declaration_id', 'tax_year', 'quarter', 'basic_tax', 'sef_tax', 'total_tax_due', 'discount_amount', 'penalty_amount', 'total_amount_due', 'amount_paid', 'balance', 'status', 'due_date'],
    'rpt_payments' => ['rpt_billing_id', 'or_no', 'amount', 'payment_date', 'payment_mode', 'status', 'collected_by', 'basic_tax', 'sef_tax', 'discount', 'penalty']
];

$missing = [];
foreach ($checks as $table => $cols) {
    if (Schema::hasTable($table)) {
        $existing = Schema::getColumnListing($table);
        foreach ($cols as $col) {
            if (!in_array($col, $existing)) {
                $missing[] = "$table.$col";
            }
        }
    } else {
        $missing[] = "TABLE NOT FOUND: $table";
    }
}

if (empty($missing)) {
    echo "ALL COLUMNS OK\n";
} else {
    echo "MISSING COLUMNS:\n" . implode("\n", $missing) . "\n";
}
