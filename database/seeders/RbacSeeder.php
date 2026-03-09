<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;

class RbacSeeder extends Seeder
{
    /**
     * Seed default modules, roles, and mark the first user as super admin.
     */
    public function run(): void
    {
        // =====================================================================
        // 1. SEED MODULES
        // =====================================================================
        $modules = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'route_name' => 'admin.dashboard.index',
                'route_prefix' => 'admin',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'BPLS',
                'slug' => 'bpls',
                'route_name' => 'bpls.index',
                'route_prefix' => 'bpls',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'RPT',
                'slug' => 'rpt',
                'route_name' => 'rpt.index',
                'route_prefix' => 'rpt',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Human Resource',
                'slug' => 'hr',
                'route_name' => 'employee-info.create',
                'route_prefix' => 'employee-info',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Treasury',
                'slug' => 'treasury',
                'route_name' => 'treasury.index',
                'route_prefix' => 'treasury',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Executive',
                'slug' => 'executive',
                'route_name' => null,
                'route_prefix' => null,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Accounting',
                'slug' => 'accounting',
                'route_name' => null,
                'route_prefix' => null,
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Agriculture Module',
                'slug' => 'agriculture',
                'route_name' => null,
                'route_prefix' => null,
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'PPMP/APP',
                'slug' => 'ppmp',
                'route_name' => null,
                'route_prefix' => null,
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Budget',
                'slug' => 'budget',
                'route_name' => null,
                'route_prefix' => null,
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'MSWD',
                'slug' => 'mswd',
                'route_name' => null,
                'route_prefix' => null,
                'sort_order' => 11,
                'is_active' => true,
            ],
        ];

        foreach ($modules as $moduleData) {
            Module::updateOrCreate(
                ['slug' => $moduleData['slug']],
                $moduleData
            );
        }

        $this->command->info('✅ Modules seeded: ' . count($modules) . ' modules.');

        // =====================================================================
        // 2. SEED DEFAULT ROLES
        // =====================================================================
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access to all modules and system settings.',
                'modules' => [], // Super admins use is_super_admin flag, not role modules
            ],
            [
                'name' => 'BPLS Staff',
                'slug' => 'bpls-staff',
                'description' => 'Access to Business Permit and Licensing System module.',
                'modules' => ['bpls'],
            ],
            [
                'name' => 'RPT Assessor',
                'slug' => 'rpt-assessor',
                'description' => 'Access to Real Property Tax module.',
                'modules' => ['rpt'],
            ],
            [
                'name' => 'HR Officer',
                'slug' => 'hr-officer',
                'description' => 'Access to Human Resource module.',
                'modules' => ['hr'],
            ],
            [
                'name' => 'Treasury Officer',
                'slug' => 'treasury-officer',
                'description' => 'Access to Treasury module.',
                'modules' => ['treasury'],
            ],
        ];

        foreach ($roles as $roleData) {
            $moduleSlugList = $roleData['modules'];
            unset($roleData['modules']);

            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            // Sync modules for this role
            if (!empty($moduleSlugList)) {
                $moduleIds = Module::whereIn('slug', $moduleSlugList)->pluck('id');
                $role->modules()->sync($moduleIds);
            }
        }

        $this->command->info('✅ Roles seeded: ' . count($roles) . ' roles.');

        // =====================================================================
        // 3. MARK FIRST USER AS SUPER ADMIN
        // =====================================================================
        $firstUser = User::orderBy('id')->first();

        if ($firstUser) {
            $firstUser->update(['is_super_admin' => true]);
            $this->command->info('✅ User "' . $firstUser->uname . '" (ID: ' . $firstUser->id . ') marked as Super Admin.');
        } else {
            $this->command->warn('⚠️  No users found. Create a user account first, then run this seeder again to mark them as Super Admin.');
        }
    }
}
