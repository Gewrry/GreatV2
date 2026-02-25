<div>
    @include('layouts.admin.navigation')

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ================================================================
                 MESSAGES
            ================================================================ --}}
            @if ($successMessage)
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl text-sm font-semibold">
                    ✅ {{ $successMessage }}
                </div>
            @endif
            @if ($errorMessage)
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl text-sm font-semibold">
                    ❌ {{ $errorMessage }}
                </div>
            @endif

            {{-- ================================================================
                 CREATE ACCOUNT FORM
            ================================================================ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-base font-bold text-gray-800">Create New Employee Account</h3>
                </div>
                <div class="p-6">
                    <form wire:submit.prevent="createAccount" class="space-y-5">

                        {{-- Employee Selection --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Select Employee <span class="text-red-500">*</span></label>
                            <select wire:model.live="employee_id"
                                class="block w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none @error('employee_id') border-red-500 @enderror">
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->employee_id }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Selected Employee Info --}}
                        @if ($selectedEmployee)
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm">
                                <p class="font-semibold text-blue-800 mb-1">{{ $selectedEmployee->first_name }} {{ $selectedEmployee->last_name }}</p>
                                <p class="text-blue-600">{{ $selectedEmployee->department->department_name ?? 'No Department' }} — {{ $selectedEmployee->designation ?? 'N/A' }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Username --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                                <input wire:model="uname" type="text" required
                                    class="block w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none @error('uname') border-red-500 @enderror"
                                    placeholder="e.g. jdoe">
                                @error('uname')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                                <input wire:model="password" type="password" required
                                    class="block w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none @error('password') border-red-500 @enderror"
                                    placeholder="Min. 8 characters">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                                <input wire:model="password_confirmation" type="password" required
                                    class="block w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none">
                            </div>
                        </div>

                        {{-- Role Assignment --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Assign Roles</label>
                            @if($roles->isEmpty())
                                <p class="text-xs text-gray-400 italic">No roles available. <a href="{{ route('admin.roles.index') }}" class="text-logo-teal underline">Create roles first</a>.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-40 overflow-y-auto pr-1">
                                    @foreach($roles as $role)
                                        <label class="flex items-start gap-2 p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                                            <input type="checkbox"
                                                wire:model="selectedRoles"
                                                value="{{ $role->id }}"
                                                class="w-4 h-4 mt-0.5 text-logo-teal border-gray-300 rounded focus:ring-logo-teal">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-700">{{ $role->name }}</div>
                                                @if($role->modules->isNotEmpty())
                                                    <div class="text-xs text-gray-400">{{ $role->modules->pluck('name')->join(', ') }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Super Admin Toggle --}}
                        <div class="flex items-center gap-3 p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                            <input type="checkbox" wire:model="is_super_admin" id="is_super_admin"
                                class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            <label for="is_super_admin" class="text-sm font-semibold text-yellow-800 cursor-pointer">
                                Grant Super Admin access (bypasses all module restrictions)
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2.5 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-logo-teal/80 transition shadow">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================================================================
                 STATISTICS
            ================================================================ --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl">
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-wider">Total Accounts</p>
                    <p class="text-3xl font-bold text-blue-700 mt-1">{{ $totalAccounts }}</p>
                </div>
                <div class="bg-green-50 border border-green-100 p-4 rounded-2xl">
                    <p class="text-xs font-bold text-green-500 uppercase tracking-wider">This Month</p>
                    <p class="text-3xl font-bold text-green-700 mt-1">{{ $accountsThisMonth }}</p>
                </div>
                <div class="bg-purple-50 border border-purple-100 p-4 rounded-2xl">
                    <p class="text-xs font-bold text-purple-500 uppercase tracking-wider">Departments</p>
                    <p class="text-3xl font-bold text-purple-700 mt-1">{{ $uniqueDepartments }}</p>
                </div>
            </div>

            {{-- ================================================================
                 ACCOUNTS LIST
            ================================================================ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-800">Existing Accounts</h3>
                    <div class="flex items-center gap-3">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Search name or username..."
                            class="border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none w-56">
                        <select wire:model.live="department"
                            class="border border-gray-300 rounded-xl px-3 py-1.5 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="resetFilters"
                            class="px-3 py-1.5 text-xs font-semibold text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                            Reset
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Roles / Access</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($accounts as $account)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-sm text-gray-800">
                                            {{ $account->employee->first_name ?? '' }} {{ $account->employee->last_name ?? '' }}
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $account->employee->employee_id ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-sm text-gray-700">{{ $account->uname }}</span>
                                        @if($account->is_super_admin)
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800">
                                                ⭐ Super Admin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $account->employee->department->department_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($account->is_super_admin)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                All Modules
                                            </span>
                                        @elseif($account->roles->isEmpty())
                                            <span class="text-xs text-gray-400 italic">No roles assigned</span>
                                        @else
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($account->roles as $role)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-logo-teal/10 text-logo-teal border border-logo-teal/20">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        {{ $account->encoded_date ? $account->encoded_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Edit Roles --}}
                                            <button wire:click="openEditRoles({{ $account->id }})"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-logo-teal border border-logo-teal rounded-lg hover:bg-logo-teal hover:text-white transition">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                Roles
                                            </button>
                                            {{-- Delete --}}
                                            <button wire:click="deleteAccount({{ $account->id }})"
                                                onclick="return confirm('Delete account for {{ addslashes($account->uname) }}?')"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-600 border border-red-300 rounded-lg hover:bg-red-600 hover:text-white transition">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        <p class="font-medium">No accounts found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($accounts->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $accounts->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ================================================================
         EDIT ROLES MODAL (Livewire-driven)
    ================================================================ --}}
    @if($editUserId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Edit Roles & Access</h3>
                    <button wire:click="$set('editUserId', null)" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">

                    {{-- Roles --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Assign Roles</label>
                        @if($roles->isEmpty())
                            <p class="text-xs text-gray-400 italic">No roles available.</p>
                        @else
                            <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                                @foreach($roles as $role)
                                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                                        <input type="checkbox"
                                            wire:model="editSelectedRoles"
                                            value="{{ $role->id }}"
                                            class="w-4 h-4 text-logo-teal border-gray-300 rounded focus:ring-logo-teal">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-700">{{ $role->name }}</div>
                                            @if($role->modules->isNotEmpty())
                                                <div class="text-xs text-gray-400">{{ $role->modules->pluck('name')->join(', ') }}</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Super Admin Toggle --}}
                    <div class="flex items-center gap-3 p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <input type="checkbox" wire:model="editIsSuperAdmin" id="editIsSuperAdmin"
                            class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                        <label for="editIsSuperAdmin" class="text-sm font-semibold text-yellow-800 cursor-pointer">
                            Super Admin (full access to all modules)
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <button wire:click="$set('editUserId', null)"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button wire:click="saveRoles"
                            class="px-4 py-2 text-sm font-semibold text-white bg-logo-teal rounded-xl hover:bg-logo-teal/80 transition">
                            Save Roles
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
