<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    /**
     * Display a listing of modules.
     */
    public function index()
    {
        $modules = Module::withCount('roles')->ordered()->get();

        return view('modules.admin.module_management.index', compact('modules'));
    }

    /**
     * Store a newly created module.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:modules,name',
            'slug' => 'nullable|string|max:100|unique:modules,slug',
            'route_name' => 'nullable|string|max:255',
            'route_prefix' => 'nullable|string|max:100',
            'icon_svg' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Module::create($validated);

        return response()->json(['success' => true, 'message' => 'Module created successfully.']);
    }

    /**
     * Update the specified module.
     */
    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:modules,name,' . $module->id,
            'route_name' => 'nullable|string|max:255',
            'route_prefix' => 'nullable|string|max:100',
            'icon_svg' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $module->update($validated);

        return response()->json(['success' => true, 'message' => 'Module updated successfully.']);
    }

    /**
     * Remove the specified module.
     */
    public function destroy(Module $module)
    {
        // Detach all roles before deleting
        $module->roles()->detach();
        $module->delete();

        return response()->json(['success' => true, 'message' => 'Module deleted successfully.']);
    }

    /**
     * Toggle the active status of a module.
     */
    public function toggle(Module $module)
    {
        $module->update(['is_active' => !$module->is_active]);

        $status = $module->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'is_active' => $module->is_active,
            'message' => 'Module "' . $module->name . '" has been ' . $status . '.',
        ]);
    }
}
