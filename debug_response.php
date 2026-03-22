<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Http\Controllers\BusinessListController;
use Illuminate\Http\Request;

$req = new Request(['status' => 'retired']);
$c = new BusinessListController();
$res = $c->search($req);
$data = json_decode($res->getContent(), true);

foreach ($data['data'] as $item) {
    if ($item['id'] == 28) {
        echo "Entry 28 Found in Search Response:\n";
        echo "Retirement Date: " . ($item['retirement_date'] ?? 'MISSING') . "\n";
        echo "Retirement Reason: " . ($item['retirement_reason'] ?? 'MISSING') . "\n";
        echo "Full Item for 28:\n";
        print_r($item);
    }
}
