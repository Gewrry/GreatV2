<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeInfo;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Show the form for creating a new account.
     */
/**
 * Show the form for creating a new account.
 */
public function create()
{
    // Get employees who don't have accounts yet
    $employees = EmployeeInfo::whereDoesntHave('user')
        ->with('department')
        ->get();

    // Get all departments for any future use (if needed in the form)
    $departments = Department::orderBy('department_name')->get();

    return view('accounts.create', compact('employees', 'departments'));
}

    /**
     * Store a newly created account.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee_info,id|unique:users,employee_id',
            'uname' => 'required|string|max:255|unique:users,uname',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create the user account
            $user = User::create([
                'employee_id' => $request->employee_id,
                'uname' => $request->uname,
                'password' => Hash::make($request->password),
                'encoded_date' => now(),
                'encoded_by' => auth()->id(),
            ]);

            return redirect()->route('accounts.create')
                ->with('success', 'Account created successfully for employee.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating account: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display a listing of accounts.
     */
    public function index(Request $request)
    {
        // Start query
        $query = User::with(['employee.department', 'encodedBy']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('uname', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('employee_id', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->input('department'));
            });
        }

        // Apply sorting
        if ($request->filled('sort')) {
            $direction = $request->input('direction', 'asc');

            switch ($request->input('sort')) {
                case 'employee_id':
                    $query->join('employee_info', 'users.employee_id', '=', 'employee_info.id')
                        ->orderBy('employee_info.employee_id', $direction)
                        ->select('users.*');
                    break;
                case 'name':
                    $query->join('employee_info', 'users.employee_id', '=', 'employee_info.id')
                        ->orderBy('employee_info.last_name', $direction)
                        ->orderBy('employee_info.first_name', $direction)
                        ->select('users.*');
                    break;
                default:
                    $query->orderBy($request->input('sort'), $direction);
            }
        } else {
            $query->orderBy('encoded_date', 'desc');
        }

        // Get paginated results
        $accounts = $query->paginate(10)->withQueryString();

        // Get all departments for filter dropdown
        $departments = Department::orderBy('department_name')->get();

        // Get statistics
        $totalAccounts = User::count();
        $accountsThisMonth = User::whereMonth('encoded_date', now()->month)
            ->whereYear('encoded_date', now()->year)
            ->count();
        $uniqueDepartments = User::join('employee_info', 'users.employee_id', '=', 'employee_info.id')
            ->join('departments', 'employee_info.department_id', '=', 'departments.id')
            ->distinct('departments.id')
            ->count('departments.id');

        return view('accounts.index', compact('accounts', 'departments', 'totalAccounts', 'accountsThisMonth', 'uniqueDepartments'));
    }

    /**
     * Check if username is available (AJAX).
     */
    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        $exists = User::where('uname', $username)->exists();

        return response()->json(['available' => !$exists]);
    }

    /**
     * Check if employee already has an account (AJAX).
     */
    public function checkEmployee(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $exists = User::where('employee_id', $employeeId)->exists();

        return response()->json(['has_account' => $exists]);
    }

    /**
     * Show account details (AJAX).
     */
    public function show($id)
    {
        $account = User::with(['employee.department', 'encodedBy'])->findOrFail($id);

        return response()->json([
            'employee_id' => $account->employee->employee_id ?? null,
            'full_name' => ($account->employee->first_name ?? '') . ' ' .
                ($account->employee->last_name ?? ''),
            'email' => $account->employee->email ?? null,
            'contact_number' => $account->employee->contact_number ?? null,
            'username' => $account->uname,
            'department' => $account->employee->department->department_name ?? null,
            'designation' => $account->employee->designation ?? null,
            'created_at' => $account->encoded_date->format('M d, Y H:i'),
            'hire_date' => $account->employee->hire_date ? $account->employee->hire_date->format('M d, Y') : null,
            'employee_group' => $account->employee->employee_group ?? null,
        ]);
    }

    /**
     * Delete an account.
     */
    public function destroy($id)
    {
        try {
            $account = User::findOrFail($id);
            $account->delete();

            return redirect()->route('accounts.index')
                ->with('success', 'Account deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('accounts.index')
                ->with('error', 'Error deleting account: ' . $e->getMessage());
        }
    }
}