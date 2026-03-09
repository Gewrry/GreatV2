# Role-Based Access Control (RBAC) Implementation Plan

## Overview

Implement a full RBAC system where:
- **Super Admin** users have unrestricted access to all modules
- **Admin** can create/manage roles and assign modules to roles
- **Admin** can assign multiple roles to users
- Users can only access modules their assigned roles permit
- The sidebar dynamically shows only accessible modules

---

## Database Schema

### New Tables

#### `roles`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | e.g. "BPLS Staff" |
| `slug` | string unique | e.g. "bpls-staff" |
| `description` | text nullable | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

#### `modules`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | e.g. "BPLS" |
| `slug` | string unique | e.g. "bpls" |
| `route_prefix` | string nullable | e.g. "bpls" |
| `icon` | string nullable | SVG path or icon class |
| `sort_order` | integer default 0 | For sidebar ordering |
| `is_active` | boolean default true | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

#### `role_module` (pivot)
| Column | Type | Notes |
|---|---|---|
| `role_id` | bigint FK | references roles.id |
| `module_id` | bigint FK | references modules.id |

#### `role_user` (pivot)
| Column | Type | Notes |
|---|---|---|
| `role_id` | bigint FK | references roles.id |
| `user_id` | bigint FK | references users.id |

### Modified Tables

#### `users` (new column)
| Column | Type | Notes |
|---|---|---|
| `is_super_admin` | boolean default false | Bypasses all module checks |

---

## Architecture Flow

```
HTTP Request
    → auth middleware (must be logged in)
    → ModuleAccess middleware (checks module slug)
        → if is_super_admin → ALLOW
        → else check user roles → check role modules
            → if module found in any role → ALLOW
            → else → 403 Forbidden
```

---

## Modules to Register

| Slug | Display Name | Route Prefix | Status |
|---|---|---|---|
| `admin` | Admin | `/admin` | Active (Super Admin only) |
| `bpls` | BPLS | `/bpls` | Active |
| `rpt` | RPT | `/rpt` | Active |
| `hr` | Human Resource | `/employee-info` | Active |
| `treasury` | Treasury | `/treasury` | Active |
| `executive` | Executive | — | Placeholder |
| `accounting` | Accounting | — | Placeholder |
| `agriculture` | Agriculture Module | — | Placeholder |
| `ppmp` | PPMP/APP | — | Placeholder |
| `budget` | Budget | — | Placeholder |
| `mswd` | MSWD | — | Placeholder |

---

## Files to Create / Modify

### New Migrations
1. `database/migrations/YYYY_MM_DD_create_roles_table.php`
2. `database/migrations/YYYY_MM_DD_create_modules_table.php`
3. `database/migrations/YYYY_MM_DD_create_role_module_table.php`
4. `database/migrations/YYYY_MM_DD_create_role_user_table.php`
5. `database/migrations/YYYY_MM_DD_add_is_super_admin_to_users_table.php`

### New Models
1. `app/Models/Role.php` — belongsToMany(Module), belongsToMany(User)
2. `app/Models/Module.php` — belongsToMany(Role)

### Modified Models
1. `app/Models/User.php` — add belongsToMany(Role), isSuperAdmin(), hasModuleAccess($slug)

### New Middleware
1. `app/Http/Middleware/ModuleAccess.php` — checks module slug access

### New Controllers
1. `app/Http/Controllers/Admin/RoleController.php` — CRUD roles + assign modules
2. `app/Http/Controllers/Admin/ModuleController.php` — CRUD modules

### Modified Controllers / Livewire
1. `app/Http/Livewire/Admin/AccountsManager.php` — add role assignment UI

### New Views
1. `resources/views/modules/admin/role_management/index.blade.php`
2. `resources/views/modules/admin/module_management/index.blade.php`

### Modified Views
1. `resources/views/layouts/admin/app.blade.php` — conditional sidebar links

### Modified Routes
1. `routes/web.php` — add `module:slug` middleware to each module group + new admin routes

### New Seeders
1. `database/seeders/RbacSeeder.php` — default roles, modules, super admin flag

---

## Implementation Steps (Ordered)

1. **Migration: add is_super_admin to users**
2. **Migration: create roles table**
3. **Migration: create modules table**
4. **Migration: create role_module pivot table**
5. **Migration: create role_user pivot table**
6. **Model: Role** with relationships
7. **Model: Module** with relationships
8. **Model: User** — add roles relationship + helper methods
9. **Middleware: ModuleAccess** — register in bootstrap/app.php
10. **Routes: web.php** — apply middleware + add admin routes for roles/modules
11. **Controller: RoleController** — index, store, update, destroy, assignModules
12. **Controller: ModuleController** — index, store, update, destroy, toggle
13. **Livewire: AccountsManager** — add roles field, sync roles on create/update
14. **View: role_management/index.blade.php** — list roles, assign modules via checkboxes
15. **View: module_management/index.blade.php** — list modules, toggle active
16. **View: layouts/admin/app.blade.php** — wrap each sidebar link with module access check
17. **Seeder: RbacSeeder** — seed default modules, roles, set first user as super admin

---

## Key Code Patterns

### User Model Helper Methods
```php
public function roles()
{
    return $this->belongsToMany(Role::class, 'role_user');
}

public function isSuperAdmin(): bool
{
    return (bool) $this->is_super_admin;
}

public function hasModuleAccess(string $slug): bool
{
    if ($this->isSuperAdmin()) return true;
    return $this->roles()
        ->whereHas('modules', fn($q) => $q->where('slug', $slug)->where('is_active', true))
        ->exists();
}
```

### ModuleAccess Middleware
```php
public function handle(Request $request, Closure $next, string $moduleSlug): Response
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    if (!auth()->user()->hasModuleAccess($moduleSlug)) {
        abort(403, 'You do not have access to this module.');
    }
    return $next($request);
}
```

### Route Protection Example
```php
Route::prefix('bpls')->name('bpls.')->middleware('module:bpls')->group(function () {
    // ... bpls routes
});
```

### Sidebar Conditional Display
```blade
@if(auth()->user()->isSuperAdmin() || auth()->user()->hasModuleAccess('bpls'))
    <a href="{{ route('bpls.index') }}" ...>BPLS</a>
@endif
```
