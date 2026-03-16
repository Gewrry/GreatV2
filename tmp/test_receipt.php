<?php
$payment = \App\Models\RPT\RptPayment::latest('id')->first();
if ($payment) {
    try {
        $payment->load(['billing.taxDeclaration.property', 'collectedBy']);
        $html = view('modules.treasury.rpt_payments.receipt', compact('payment'))->render();
        echo "SUCCESS: " . strlen($html) . " bytes\n";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "No payment found.\n";
}
