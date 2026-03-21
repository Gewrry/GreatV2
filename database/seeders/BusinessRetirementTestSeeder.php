<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\onlineBPLS\BplsOnlineApplication;
use Carbon\Carbon;

class BusinessRetirementTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Get an application for the client to retire (must be 'approved')
        $clientApp = BplsOnlineApplication::orderBy('id', 'asc')->skip(0)->first();
        if ($clientApp) {
            $clientApp->update([
                'workflow_status' => 'approved',
                'permit_year' => Carbon::now()->year,
                'assessment_amount' => 1500.00,
                // Make sure there is no outstanding balance by clearing things or saying it's paid
                // The easiest way is to ensure no unpaid bills exist, which we can't fully control here, 
                // but setting it to approved usually means it's fully paid for the year.
            ]);
            $this->command->info("Set App #" . $clientApp->application_number . " to 'approved' for testing client-side retirement.");
        }

        // 2. Get an application for the staff to approve the retirement for (must be 'retirement_requested')
        $staffApp = BplsOnlineApplication::orderBy('id', 'asc')->skip(1)->first();
        if ($staffApp) {
            $staffApp->update([
                'workflow_status' => 'retirement_requested',
                'retirement_reason' => 'Closure of Business due to testing',
                'retirement_date' => Carbon::parse('2026-03-01')->format('Y-m-d'),
                'retirement_remarks' => 'Created via seeder for testing staff-side retirement approval.',
            ]);
            $this->command->info("Set App #" . $staffApp->application_number . " to 'retirement_requested' for testing staff-side appoval in Change Status.");
        }
        
        $this->command->info("Done! You can now test both flows.");
    }
}
