<?php
// database/migrations/2026_02_20_000001_create_fee_rules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Bpls\FeeRule;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fee_rules', function (Blueprint $table) {
            $table->id();

            $table->string('name');                    // Display name, e.g. "Gross Sales Tax (LBT)"

            // What the formula is applied against:
            //   gross_sales | scale | flat
            $table->string('base_type')->default('flat');

            // How the amount is calculated:
            //   graduated_rate | scale_table | flat_amount | percentage
            $table->string('formula_type');

            // Used when formula_type = 'flat_amount'
            $table->decimal('flat_amount', 12, 4)->nullable();

            // Used when formula_type = 'percentage'  (e.g. 1.75 = 1.75%)
            $table->decimal('percentage', 8, 4)->nullable();

            // Used when formula_type = 'graduated_rate'
            // JSON array of { max: float|null, rate: float }
            // Last entry should have max = null (catch-all)
            $table->json('rate_table')->nullable();

            // Used when formula_type = 'scale_table'
            // JSON object keyed by scale code 1–5
            // { "1": 500, "2": 1000, "3": 2000, "4": 3000, "5": 5000 }
            $table->json('scale_table')->nullable();

            $table->text('notes')->nullable();         // Reference to memo / section

            $table->unsignedSmallInteger('sort_order')->default(0);  // Display & compute order
            $table->boolean('enabled')->default(true);               // Toggle without deleting

            $table->timestamps();
        });

        // ── Seed default LGU rules ──────────────────────────────────────────
        $now = now();
        $rules = FeeRule::defaultRules();

        foreach ($rules as &$rule) {
            $rule['rate_table'] = $rule['rate_table'] ? json_encode($rule['rate_table']) : null;
            $rule['scale_table'] = $rule['scale_table'] ? json_encode($rule['scale_table']) : null;
            $rule['created_at'] = $now;
            $rule['updated_at'] = $now;
        }

        DB::table('fee_rules')->insert($rules);
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_rules');
    }
};