<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BplsBenefit;

class BplsBenefitsSeeder extends Seeder
{
    public function run(): void
    {
        $benefits = [
            [
                'name' => 'PWD',
                'label' => 'Persons with Disability',
                'field_key' => 'is_pwd',
                'discount_percent' => 20.00,
                'description' => 'Registered persons with disability under RA 9442.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '4PS',
                'label' => 'Pantawid Pamilyang Pilipino Program',
                'field_key' => 'is_4ps',
                'discount_percent' => 20.00,
                'description' => 'Beneficiaries of the 4Ps government program.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Solo Parent',
                'label' => 'Solo Parent',
                'field_key' => 'is_solo_parent',
                'discount_percent' => 10.00,
                'description' => 'Registered solo parents under RA 8972.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Senior Citizen',
                'label' => 'Senior Citizen',
                'field_key' => 'is_senior',
                'discount_percent' => 20.00,
                'description' => 'Senior citizens aged 60 and above under RA 9994.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            // COVID-19 vaccine discounts are intentionally excluded.
            // To re-add any temporary benefit, use the Benefits management UI.
        ];

        foreach ($benefits as $data) {
            BplsBenefit::updateOrCreate(['field_key' => $data['field_key']], $data);
        }
    }
}