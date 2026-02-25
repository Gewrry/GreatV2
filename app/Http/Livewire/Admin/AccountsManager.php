<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\EmployeeInfo;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AccountsManager extends Component
{
    use WithPagination;

    protected $layout = 'layouts.admin.app';

    /**
     * Livewire automatically casts these properties to the specified types.
     */
    protected $casts = [
        'selectedRoles' => 'array',
        'editSelectedRoles' => 'array',
        'is_super_admin' => 'boolean',
        'editIsSuperAdmin' => 'boolean',
    ];

    // =========================================================================
    // Create Account Form Fields
    // =========================================================================
    public $employee_id = '';
    public $uname = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRoles = [];   // array of role IDs for new account
    public $is_super_admin = false;

    // =========================================================================
    // Edit Account Fields
    // =========================================================================
    public $editUserId = null;
    public $editSelectedRoles = [];
    public $editIsSuperAdmin = false;

    // =========================================================================
    // Search and Filter
    // =========================================================================
    public $search = '';
    public $department = '';

    // =========================================================================
    // Selected employee info (for display)
    // =========================================================================
    public $selectedEmployee = null;

    // =========================================================================
    // Messages
    // =========================================================================
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'employee_id' => 'required|exists:employee_info,id|unique:users,employee_id',
        'uname' => 'required|string|max:255|unique:users,uname',
        'password' => 'required|string|min:8|confirmed',
        'selectedRoles' => 'nullable|array',
        'selectedRoles.*' => 'exists:roles,id',
        'is_super_admin' => 'boolean',
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
            $user = User::create([
                'employee_id' => $this->employee_id,
                'uname' => $this->uname,
                'password' => Hash::make($this->password),
                'encoded_date' => now(),
                'encoded_by' => auth()->id(),
                'is_super_admin' => $this->is_super_admin,
            ]);

            // Assign roles
            if (!empty($this->selectedRoles)) {
                $user->roles()->sync($this->selectedRoles);
            }

            // Clear form
            $this->reset([
                'employee_id',
                'uname',
                'password',
                'password_confirmation',
                'selectedEmployee',
                'selectedRoles',
                'is_super_admin',
            ]);

            $this->successMessage = 'Account created successfully!';
            $this->errorMessage = '';
            $this->resetPage();

        } catch (\Exception $e) {
            $this->errorMessage = 'Error creating account: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function deleteAccount($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->roles()->detach();
            $user->delete();

            $this->successMessage = 'Account deleted successfully!';
            $this->errorMessage = '';
        } catch (\Exception $e) {
            $this->errorMessage = 'Error deleting account: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }

    /**
     * Open the edit roles modal for a user.
     */
    public function openEditRoles($userId)
    {
        $user = User::with(['roles.modules'])->findOrFail($userId);
        $this->editUserId = $userId;
        $this->editSelectedRoles = $user->roles->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->editIsSuperAdmin = (bool) $user->is_super_admin;
    }

    /**
     * Save the updated roles for a user.
     */
    public function saveRoles()
    {
        $this->validate([
            'editSelectedRoles' => 'nullable|array',
            'editSelectedRoles.*' => 'exists:roles,id',
            'editIsSuperAdmin' => 'boolean',
        ]);

        try {
            $user = User::findOrFail($this->editUserId);
            $user->roles()->sync($this->editSelectedRoles ?? []);
            $user->update(['is_super_admin' => $this->editIsSuperAdmin]);

            $this->reset(['editUserId', 'editSelectedRoles', 'editIsSuperAdmin']);
            $this->successMessage = 'Roles updated successfully!';
            $this->errorMessage = '';

        } catch (\Exception $e) {
            $this->errorMessage = 'Error updating roles: ' . $e->getMessage();
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
        $query = User::with(['employee.department', 'encodedBy', 'roles']);

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

        // Get all roles for assignment (with modules for display)
        $roles = Role::with('modules')->orderBy('name')->get();

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
            'roles',
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
