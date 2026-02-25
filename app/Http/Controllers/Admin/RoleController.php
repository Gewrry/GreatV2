<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('modules')->withCount('users')->orderBy('name')->get();
        $modules = Module::active()->ordered()->get();

        return view('modules.admin.role_management.index', compact('roles', 'modules'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Role::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        Role::create($validated);

        return response()->json(['success' => true, 'message' => 'Role created successfully.']);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
        ]);

        $role->update($validated);

        return response()->json(['success' => true, 'message' => 'Role updated successfully.']);
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Detach all users and modules before deleting
        $role->users()->detach();
        $role->modules()->detach();
        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
    }

    /**
     * Assign modules to a role (sync).
     */
    public function assignModules(Request $request, Role $role)
    {
        $validated = $request->validate([
            'module_ids' => 'nullable|array',
            'module_ids.*' => 'exists:modules,id',
        ]);

        $moduleIds = $validated['module_ids'] ?? [];
        $role->modules()->sync($moduleIds);

        return response()->json([
            'success' => true,
            'message' => 'Modules assigned to role "' . $role->name . '" successfully.',
        ]);
    }
}
