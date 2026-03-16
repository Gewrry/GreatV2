<?php
echo "Modules:\n";
foreach (\App\Models\Module::ordered()->get() as $m) {
    echo $m->slug . " (" . $m->sort_order . ")\n";
}

echo "\nAccessible by Admin:\n";
$role = \App\Models\Role::where('slug', 'super-admin')->first();
if ($role) {
    echo "Super-Admin modules: " . implode(', ', $role->modules()->pluck('slug')->toArray()) . "\n";
}

$user = \App\Models\User::find(4);
if ($user) {
    echo "\nUser 4 Modules: " . implode(', ', $user->accessibleModules()->pluck('slug')->toArray()) . "\n";
}
