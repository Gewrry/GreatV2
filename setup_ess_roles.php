<?php
$essModule = \App\Models\Module::where('slug', 'employee_portal')->first();
if ($essModule) {
    $roles = \App\Models\Role::all();
    foreach ($roles as $role) {
        if (!$role->modules()->where('module_id', $essModule->id)->exists()) {
            $role->modules()->attach($essModule->id);
        }
    }
    echo "ESS module attached to all roles successfully.\n";
} else {
    echo "Employee Portal module not found.\n";
}
