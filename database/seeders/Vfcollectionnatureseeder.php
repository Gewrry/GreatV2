<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VF\CollectionNature;

class VfCollectionNatureSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Franchise Fee', 'account_code' => '1-01-01', 'default_amount' => 500.00, 'sort_order' => 1],
            ['name' => 'Sticker Fee', 'account_code' => '1-01-02', 'default_amount' => 100.00, 'sort_order' => 2],
            ['name' => 'Penalty', 'account_code' => '1-01-03', 'default_amount' => 0.00, 'sort_order' => 3],
            ['name' => 'MTOP Fee', 'account_code' => '1-01-04', 'default_amount' => 200.00, 'sort_order' => 4],
            ['name' => 'Drivers ID Fee', 'account_code' => '1-01-05', 'default_amount' => 150.00, 'sort_order' => 5],
            ['name' => 'Garage Inspection Fee', 'account_code' => '1-01-06', 'default_amount' => 50.00, 'sort_order' => 6],
        ];

        foreach ($items as $item) {
            CollectionNature::firstOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['is_active' => true])
            );
        }
    }
}