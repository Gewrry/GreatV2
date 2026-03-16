<?php
echo "MODULES LIST:\n";
$modules = \App\Models\Module::ordered()->get();
foreach ($modules as $m) {
    echo sprintf("[%d] Slug: %-20s Name: %-20s Order: %d Active: %d\n", 
        $m->id, $m->slug, $m->name, $m->sort_order, $m->is_active);
}

$user = \App\Models\User::find(4); // Assuming this is the user
if ($user) {
    echo "\nUSER 4 PERMISSIONS:\n";
    echo "Is Super Admin: " . ($user->isSuperAdmin() ? 'YES' : 'NO') . "\n";
    echo "Accessible Modules: " . $user->accessibleModules()->pluck('slug')->implode(', ') . "\n";
}
