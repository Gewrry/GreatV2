<?php
$users = \App\Models\User::all();
foreach ($users as $user) {
    if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
        echo "User ID: " . $user->id . " - " . $user->name . "\n";
        echo "Roles: " . json_encode($user->roles()->pluck('slug')) . "\n";
        echo "Modules: " . json_encode($user->accessibleModules()->pluck('slug')) . "\n\n";
    }
}
