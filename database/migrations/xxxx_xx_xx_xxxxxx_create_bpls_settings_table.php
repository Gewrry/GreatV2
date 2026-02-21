<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Check if table doesn't exist before creating
        if (!Schema::hasTable('bpls_settings')) {
            Schema::create('bpls_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            // Insert default settings only if table was just created
            DB::table('bpls_settings')->insert([
                ['key' => 'advance_discount_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'advance_discount_days_before', 'value' => '30', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'advance_discount_annual', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'advance_discount_semi_annual', 'value' => '8', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'advance_discount_quarterly', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ]);
        } else {
            // Table exists, maybe insert default settings if they don't exist
            $existingKeys = DB::table('bpls_settings')->pluck('key')->toArray();
            $defaultSettings = [
                ['key' => 'advance_discount_enabled', 'value' => '0'],
                ['key' => 'advance_discount_days_before', 'value' => '30'],
                ['key' => 'advance_discount_annual', 'value' => '10'],
                ['key' => 'advance_discount_semi_annual', 'value' => '8'],
                ['key' => 'advance_discount_quarterly', 'value' => '5'],
            ];

            foreach ($defaultSettings as $setting) {
                if (!in_array($setting['key'], $existingKeys)) {
                    DB::table('bpls_settings')->insert([
                        'key' => $setting['key'],
                        'value' => $setting['value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('bpls_settings');
    }
};