@include('partials.header')

<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Role Management</h2>
                <p class="text-sm text-gray-500 mt-1">Create roles and assign modules to control user access.</p>
            </div>
            <button onclick="document.getElementById('createRoleModal').classList.remove('hidden')"
                class="inline-flex items-center px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl shadow hover:bg-logo-teal/80 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Role
            </button>
        </div>
    </x-slot>

    {{-- Roles Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Description
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Modules
                        Assigned</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Users</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $role->name }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $role->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $role->description ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($role->modules as $module)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-logo-teal/10 text-logo-teal border border-logo-teal/20">
                                        {{ $module->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">No modules assigned</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                {{ $role->users_count }} user{{ $role->users_count !== 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Assign Modules --}}
                                <button
                                    onclick="openAssignModules({{ $role->id }}, '{{ addslashes($role->name) }}', {{ $role->modules->pluck('id')->toJson() }})"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-logo-teal border border-logo-teal rounded-lg hover:bg-logo-teal hover:text-white transition">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Modules
                                </button>
                                {{-- Edit --}}
                                <button
                                    onclick="openEditRole({{ $role->id }}, '{{ addslashes($role->name) }}', '{{ addslashes($role->description ?? '') }}')"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                    Edit
                                </button>
                                {{-- Delete --}}
                                <button onclick="deleteRole({{ $role->id }}, '{{ addslashes($role->name) }}')"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-600 border border-red-300 rounded-lg hover:bg-red-600 hover:text-white transition">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="font-medium">No roles found</p>
                            <p class="text-sm mt-1">Create your first role to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ===================================================================
    CREATE ROLE MODAL
    =================================================================== --}}
    <div id="createRoleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Create New Role</h3>
                <button onclick="document.getElementById('createRoleModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="createRoleForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="createRoleName" required
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none"
                        placeholder="e.g. BPLS Staff">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="createRoleDesc" rows="3"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none resize-none"
                        placeholder="Optional description..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('createRoleModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-logo-teal rounded-xl hover:bg-logo-teal/80 transition">
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================================================================
    EDIT ROLE MODAL
    =================================================================== --}}
    <div id="editRoleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Edit Role</h3>
                <button onclick="document.getElementById('editRoleModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editRoleForm" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="editRoleId">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="editRoleName" required
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea id="editRoleDesc" rows="3"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('editRoleModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================================================================
    ASSIGN MODULES MODAL
    =================================================================== --}}
    <div id="assignModulesModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Assign Modules</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Role: <span id="assignRoleName"
                            class="font-semibold text-logo-teal"></span></p>
                </div>
                <button onclick="document.getElementById('assignModulesModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <input type="hidden" id="assignRoleId">
                <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    @foreach($modules as $module)
                        <label
                            class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition">
                            <input type="checkbox"
                                class="module-checkbox w-4 h-4 text-logo-teal border-gray-300 rounded focus:ring-logo-teal"
                                value="{{ $module->id }}" data-module-name="{{ $module->name }}">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-800">{{ $module->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $module->slug }}</div>
                            </div>
                            @if(!$module->is_active)
                                <span class="text-xs text-orange-500 font-medium">Inactive</span>
                            @endif
                        </label>
                    @endforeach
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-4">
                    <button type="button"
                        onclick="document.getElementById('assignModulesModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="button" onclick="saveModuleAssignment()"
                        class="px-4 py-2 text-sm font-semibold text-white bg-logo-teal rounded-xl hover:bg-logo-teal/80 transition">
                        Save Assignment
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // =========================================================================
            // CREATE ROLE
            // =========================================================================
            document.getElementById('createRoleForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const name = document.getElementById('createRoleName').value;
                const description = document.getElementById('createRoleDesc').value;

                fetch('{{ route('admin.roles.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name, description })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error creating role.');
                        }
                    });
            });

            // =========================================================================
            // EDIT ROLE
            // =========================================================================
            function openEditRole(id, name, description) {
                document.getElementById('editRoleId').value = id;
                document.getElementById('editRoleName').value = name;
                document.getElementById('editRoleDesc').value = description;
                document.getElementById('editRoleModal').classList.remove('hidden');
            }

            document.getElementById('editRoleForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const id = document.getElementById('editRoleId').value;
                const name = document.getElementById('editRoleName').value;
                const description = document.getElementById('editRoleDesc').value;

                fetch(`/admin/roles/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name, description })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error updating role.');
                        }
                    });
            });

            // =========================================================================
            // DELETE ROLE
            // =========================================================================
            function deleteRole(id, name) {
                if (!confirm(`Are you sure you want to delete the role "${name}"? This will remove it from all assigned users.`)) return;

                fetch(`/admin/roles/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error deleting role.');
                        }
                    });
            }

            // =========================================================================
            // ASSIGN MODULES
            // =========================================================================
            function openAssignModules(roleId, roleName, assignedModuleIds) {
                document.getElementById('assignRoleId').value = roleId;
                document.getElementById('assignRoleName').textContent = roleName;

                // Reset all checkboxes
                document.querySelectorAll('.module-checkbox').forEach(cb => {
                    cb.checked = assignedModuleIds.includes(parseInt(cb.value));
                });

                document.getElementById('assignModulesModal').classList.remove('hidden');
            }

            function saveModuleAssignment() {
                const roleId = document.getElementById('assignRoleId').value;
                const moduleIds = Array.from(document.querySelectorAll('.module-checkbox:checked'))
                    .map(cb => parseInt(cb.value));

                fetch(`/admin/roles/${roleId}/assign-modules`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ module_ids: moduleIds })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error assigning modules.');
                        }
                    });
            }
        </script>
    @endpush
</x-admin-layout>