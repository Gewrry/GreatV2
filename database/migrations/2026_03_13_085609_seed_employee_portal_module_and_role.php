<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mod = \App\Models\Module::updateOrCreate(
            ['slug' => 'employee_portal'],
            [
                'name' => 'Employee Portal',
                'route_name' => 'hr.portal.dashboard',
                'route_prefix' => 'hr/portal',
                'icon_svg' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'sort_order' => 10,
                'is_active' => true,
            ]
        );

        $role = \App\Models\Role::updateOrCreate(
            ['slug' => 'employee'],
            ['name' => 'Employee']
        );

        if (!$role->modules()->where('module_id', $mod->id)->exists()) {
            $role->modules()->attach($mod->id);
        }
    }

    public function down(): void
    {
        $role = \App\Models\Role::where('slug', 'employee')->first();
        if ($role) {
            $role->modules()->detach();
            $role->delete();
        }
        \App\Models\Module::where('slug', 'employee_portal')->delete();
    }
};
