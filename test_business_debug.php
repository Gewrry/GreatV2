<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BplsBusiness;

$business = BplsBusiness::find(38);
if ($business) {
    file_put_contents('debug_business_38.log', json_encode($business->getAttributes(), JSON_PRETTY_PRINT));
} else {
    file_put_contents('debug_business_38.log', "Business 38 not found");
}
