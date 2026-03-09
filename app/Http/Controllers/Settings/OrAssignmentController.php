<?php
// app/Http/Controllers/Settings/OrAssignmentController.php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\OrAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrAssignmentController extends Controller
{
    public function index()
    {
        $assignments = OrAssignment::latest()->paginate(10);

        return view('modules.settings.or-assignments', compact('assignments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_or' => 'required|string|max:20',
            'end_or' => 'required|string|max:20',
            'receipt_type' => 'required|in:51C,RPTA,CTC',
        ]);

        $exists = OrAssignment::where('receipt_type', $request->receipt_type)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_or', [$request->start_or, $request->end_or])
                    ->orWhereBetween('end_or', [$request->start_or, $request->end_or]);
            })->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'OR range overlaps with an existing assignment for this receipt type.');
        }

        OrAssignment::create([
            'start_or' => $request->start_or,
            'end_or' => $request->end_or,
            'user_id' => Auth::id(),
            'cashier_name' => Auth::user()->uname,
            'receipt_type' => $request->receipt_type,
        ]);

        return back()->with('success', 'OR range assigned successfully.');
    }

    public function edit(OrAssignment $orAssignment)
    {
        $assignments = OrAssignment::latest()->paginate(10);

        return view('modules.settings.or-assignments', [
            'assignments' => $assignments,
            'editing' => $orAssignment,
        ]);
    }

    public function update(Request $request, OrAssignment $orAssignment)
    {
        $request->validate([
            'start_or' => 'required|string|max:20',
            'end_or' => 'required|string|max:20',
            'receipt_type' => 'required|in:51C,RPTA,CTC',
        ]);

        $orAssignment->update([
            'start_or' => $request->start_or,
            'end_or' => $request->end_or,
            'user_id' => Auth::id(),
            'cashier_name' => Auth::user()->uname,
            'receipt_type' => $request->receipt_type,
        ]);

        return redirect()->route('bpls.settings.or-assignments.index')
            ->with('success', 'OR assignment updated successfully.');
    }

    public function destroy(OrAssignment $orAssignment)
    {
        $orAssignment->delete();

        return back()->with('success', 'OR assignment deleted.');
    }
}