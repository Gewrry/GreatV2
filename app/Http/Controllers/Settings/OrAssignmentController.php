<?php
// app/Http/Controllers/Settings/OrAssignmentController.php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\OrAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrAssignmentController extends Controller
{
    /**
     * Join users → employee_info to get cashier list.
     * users:         id, employee_id, uname
     * employee_info: id, first_name, last_name
     */
    private function getCashiers()
    {
        return DB::table('users')
            ->join('employee_info', 'users.employee_id', '=', 'employee_info.id')
            ->select(
                'users.id',
                DB::raw("CONCAT(employee_info.first_name, ' ', employee_info.last_name) as full_name"),
                'users.uname'
            )
            ->orderBy('employee_info.last_name')
            ->get();
    }

    private function getCashierName(int $userId): string
    {
        return DB::table('users')
            ->join('employee_info', 'users.employee_id', '=', 'employee_info.id')
            ->where('users.id', $userId)
            ->selectRaw("CONCAT(employee_info.first_name, ' ', employee_info.last_name) as full_name")
            ->value('full_name') ?? '';
    }

    public function index()
    {
        $assignments = OrAssignment::latest()->paginate(10);
        $cashiers = $this->getCashiers();

        return view('modules.settings.or-assignments', compact('assignments', 'cashiers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_or' => 'required|string|max:20',
            'end_or' => 'required|string|max:20',
            'user_id' => 'required|exists:users,id',
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
            'user_id' => $request->user_id,
            'cashier_name' => $this->getCashierName((int) $request->user_id),
            'receipt_type' => $request->receipt_type,
        ]);

        return back()->with('success', 'OR range assigned successfully.');
    }

    public function edit(OrAssignment $orAssignment)
    {
        $assignments = OrAssignment::latest()->paginate(10);
        $cashiers = $this->getCashiers();

        return view('modules.settings.or-assignments', [
            'assignments' => $assignments,
            'cashiers' => $cashiers,
            'editing' => $orAssignment,
        ]);
    }

    public function update(Request $request, OrAssignment $orAssignment)
    {
        $request->validate([
            'start_or' => 'required|string|max:20',
            'end_or' => 'required|string|max:20',
            'user_id' => 'required|exists:users,id',
            'receipt_type' => 'required|in:51C,RPTA,CTC',
        ]);

        $orAssignment->update([
            'start_or' => $request->start_or,
            'end_or' => $request->end_or,
            'user_id' => $request->user_id,
            'cashier_name' => $this->getCashierName((int) $request->user_id),
            'receipt_type' => $request->receipt_type,
        ]);

        return redirect()->route('or-assignments.index')
            ->with('success', 'OR assignment updated successfully.');
    }

    public function destroy(OrAssignment $orAssignment)
    {
        $orAssignment->delete();

        return back()->with('success', 'OR assignment deleted.');
    }
}