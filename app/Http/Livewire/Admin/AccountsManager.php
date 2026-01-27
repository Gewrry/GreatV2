<?php

namespace App\Http\Livewire\Admin;

use App\Http\Livewire\Livewire;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\EmployeeInfo;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AccountsManager extends Component
{
    use WithPagination;
    protected $layout = 'layouts.admin.app';
    // Form fields
    public $employee_id = '';
    public $uname = '';
    public $password = '';
    public $password_confirmation = '';

    // Search and filter
    public $search = '';
    public $department = '';

    // Selected employee info
    public $selectedEmployee = null;

    // Messages
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'employee_id' => 'required|exists:employee_info,id|unique:users,employee_id',
        'uname' => 'required|string|max:255|unique:users,uname',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'employee_id.required' => 'Please select an employee.',
        'employee_id.unique' => 'This employee already has an account.',
        'uname.required' => 'Username is required.',
        'uname.unique' => 'Username already exists.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Passwords do not match.',
    ];

    public function mount()
    {
        // Initialize if needed
    }

    public function updatedEmployeeId($value)
    {
        if ($value) {
            $this->selectedEmployee = EmployeeInfo::with('department')->find($value);
            // Auto-generate username from email
            if ($this->selectedEmployee && $this->selectedEmployee->email && !$this->uname) {
                $this->uname = strstr($this->selectedEmployee->email, '@', true);
            }
        } else {
            $this->selectedEmployee = null;
        }
    }

    public function createAccount()
    {
        $this->validate();

        try {
            User::create([
                'employee_id' => $this->employee_id,
                'uname' => $this->uname,
                'password' => Hash::make($this->password),
                'encoded_date' => now(),
                'encoded_by' => auth()->id(),
            ]);

            // Clear form
            $this->reset(['employee_id', 'uname', 'password', 'password_confirmation', 'selectedEmployee']);

            // Show success message
            $this->successMessage = 'Account created successfully!';
            $this->errorMessage = '';

            // Reset pagination to show new account
            $this->resetPage();

        } catch (\Exception $e) {
            $this->errorMessage = 'Error creating account: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function deleteAccount($id)
    {
        try {
            User::findOrFail($id)->delete();
            $this->successMessage = 'Account deleted successfully!';
            $this->errorMessage = '';
        } catch (\Exception $e) {
            $this->errorMessage = 'Error deleting account: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function render()
    {
        // Get employees without accounts
        $employees = EmployeeInfo::whereDoesntHave('user')
            ->with('department')
            ->get();

        // Build accounts query
        $query = User::with(['employee.department', 'encodedBy']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('uname', 'like', "%{$this->search}%")
                    ->orWhereHas('employee', function ($q) {
                        $q->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%")
                            ->orWhere('employee_id', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%");
                    });
            });
        }

        // Apply department filter
        if ($this->department) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', $this->department);
            });
        }

        // Get paginated accounts
        $accounts = $query->orderBy('encoded_date', 'desc')->paginate(10);

        // Get all departments for filter
        $departments = Department::orderBy('department_name')->get();

        // Statistics
        $totalAccounts = User::count();
        $accountsThisMonth = User::whereMonth('encoded_date', now()->month)
            ->whereYear('encoded_date', now()->year)
            ->count();
        $uniqueDepartments = User::join('employee_info', 'users.employee_id', '=', 'employee_info.id')
            ->join('departments', 'employee_info.department_id', '=', 'departments.id')
            ->distinct('departments.id')
            ->count('departments.id');

return view('modules.admin.account_management.accounts-manager', compact(
            'employees',
            'accounts',
            'departments',
            'totalAccounts',
            'accountsThisMonth',
            'uniqueDepartments'
        ))->layout('layouts.admin.app');
    }

    // Reset filters
    public function resetFilters()
    {
        $this->reset(['search', 'department']);
        $this->resetPage();
    }
}