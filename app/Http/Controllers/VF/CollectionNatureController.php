<?php

namespace App\Http\Controllers\VF;

use App\Http\Controllers\Controller;
use App\Models\VF\CollectionNature;
use Illuminate\Http\Request;

class CollectionNatureController extends Controller
{
    public function index()
    {
        $items = CollectionNature::orderBy('sort_order')->orderBy('name')->get();
        return view('modules.vf.collection-natures.index', compact('items'));
    }

    public function create()
    {
        return view('modules.vf.collection-natures.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'account_code' => 'nullable|string|max:50',
            'default_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['default_amount'] = $data['default_amount'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        CollectionNature::create($data);

        return redirect()->route('vf.collection-natures.index')
            ->with('success', 'Nature of Collection item created.');
    }

    public function edit(CollectionNature $nature)
    {
        return view('modules.vf.collection-natures.edit', ['item' => $nature]);
    }

    public function update(Request $request, CollectionNature $nature)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'account_code' => 'nullable|string|max:50',
            'default_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['default_amount'] = $data['default_amount'] ?? 0;

        $nature->update($data);

        return redirect()->route('vf.collection-natures.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(CollectionNature $nature)
    {
        $nature->delete();

        return redirect()->route('vf.collection-natures.index')
            ->with('success', 'Item deleted.');
    }

    public function apiList()
    {
        return response()->json(
            CollectionNature::active()->get(['id', 'name', 'account_code', 'default_amount'])
        );
    }
}