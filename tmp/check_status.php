<?php

use App\Models\RPT\FaasProperty;
use App\Models\RPT\TaxDeclaration;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$arps = ['00-0007-01357', '00-0007-01356', '00-0007-01358'];
$properties = FaasProperty::whereIn('arp_no', $arps)->get();

foreach ($properties as $p) {
    echo "Property ID: {$p->id} | ARP: {$p->arp_no} | Status: {$p->status}\n";
    $tds = TaxDeclaration::where('faas_property_id', $p->id)->get();
    foreach ($tds as $td) {
        echo "  - TD No: {$td->td_no} | Status: {$td->status} | Inactive At: {$td->inactive_at}\n";
    }
}
