@include('partials.header')

<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Module Management</h2>
                <p class="text-sm text-gray-500 mt-1">Manage system modules and their availability.</p>
            </div>
            <button onclick="document.getElementById('createModuleModal').classList.remove('hidden')"
                class="inline-flex items-center px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl shadow hover:bg-logo-teal/80 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Module
            </button>
        </div>
    </x-slot>

    {{-- Modules Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Module</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Route</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($modules as $module)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">{{ $module->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">{{ $module->slug }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $module->route_name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-center">
                                    {{ $module->sort_order }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                        {{ $module->roles_count }} role{{ $module->roles_count !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="toggleModule({{ $module->id }}, this)"
                                        data-active="{{ $module->is_active ? '1' : '0' }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold transition
                                                {{ $module->is_active
                    ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-700'
                    : 'bg-red-100 text-red-700 hover:bg-green-100 hover:text-green-700' }}">
                                        {{ $module->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            onclick="openEditModule({{ $module->id }}, '{{ addslashes($module->name) }}', '{{ addslashes($module->route_name ?? '') }}', '{{ addslashes($module->route_prefix ?? '') }}', {{ $module->sort_order }})"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                            Edit
                                        </button>
                                        <button onclick="deleteModule({{ $module->id }}, '{{ addslashes($module->name) }}')"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-600 border border-red-300 rounded-lg hover:bg-red-600 hover:text-white transition">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <p class="font-medium">No modules found</p>
                            <p class="text-sm mt-1">Run the seeder or create modules manually.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ===================================================================
    CREATE MODULE MODAL
    =================================================================== --}}
    <div id="createModuleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Create New Module</h3>
                <button onclick="document.getElementById('createModuleModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="createModuleForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Module Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="createModuleName" required
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none"
                        placeholder="e.g. BPLS">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Route Name</label>
                    <input type="text" id="createModuleRouteName"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none"
                        placeholder="e.g. bpls.index">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Route Prefix</label>
                    <input type="text" id="createModuleRoutePrefix"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none"
                        placeholder="e.g. bpls">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sort Order</label>
                    <input type="number" id="createModuleSortOrder" value="0" min="0"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('createModuleModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-logo-teal rounded-xl hover:bg-logo-teal/80 transition">
                        Create Module
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================================================================
    EDIT MODULE MODAL
    =================================================================== --}}
    <div id="editModuleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Edit Module</h3>
                <button onclick="document.getElementById('editModuleModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editModuleForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="editModuleId">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Module Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="editModuleName" required
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Route Name</label>
                    <input type="text" id="editModuleRouteName"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Route Prefix</label>
                    <input type="text" id="editModuleRoutePrefix"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sort Order</label>
                    <input type="number" id="editModuleSortOrder" min="0"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-logo-teal focus:border-logo-teal outline-none">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('editModuleModal').classList.add('hidden')"
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

    @push('scripts')
        <script>
            // =========================================================================
            // CREATE MODULE
            // =========================================================================
            document.getElementById('createModuleForm').addEventListener('submit', function (e) {
                e.preventDefault();
                fetch('{{ route('admin.modules.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        name: document.getElementById('createModuleName').value,
                        route_name: document.getElementById('createModuleRouteName').value,
                        route_prefix: document.getElementById('createModuleRoutePrefix').value,
                        sort_order: document.getElementById('createModuleSortOrder').value,
                    })
                })
                    .then(r => r.json())
                    .then(data => { if (data.success) location.reload(); else alert(data.message); });
            });

            // =========================================================================
            // EDIT MODULE
            // =========================================================================
            function openEditModule(id, name, routeName, routePrefix, sortOrder) {
                document.getElementById('editModuleId').value = id;
                document.getElementById('editModuleName').value = name;
                document.getElementById('editModuleRouteName').value = routeName;
                document.getElementById('editModuleRoutePrefix').value = routePrefix;
                document.getElementById('editModuleSortOrder').value = sortOrder;
                document.getElementById('editModuleModal').classList.remove('hidden');
            }

            document.getElementById('editModuleForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const id = document.getElementById('editModuleId').value;
                fetch(`/admin/modules/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        name: document.getElementById('editModuleName').value,
                        route_name: document.getElementById('editModuleRouteName').value,
                        route_prefix: document.getElementById('editModuleRoutePrefix').value,
                        sort_order: document.getElementById('editModuleSortOrder').value,
                    })
                })
                    .then(r => r.json())
                    .then(data => { if (data.success) location.reload(); else alert(data.message); });
            });

            // =========================================================================
            // DELETE MODULE
            // =========================================================================
            function deleteModule(id, name) {
                if (!confirm(`Delete module "${name}"? This will remove it from all roles.`)) return;
                fetch(`/admin/modules/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                    .then(r => r.json())
                    .then(data => { if (data.success) location.reload(); else alert(data.message); });
            }

            // =========================================================================
            // TOGGLE MODULE STATUS
            // =========================================================================
            function toggleModule(id, btn) {
                fetch(`/admin/modules/${id}/toggle`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            btn.dataset.active = data.is_active ? '1' : '0';
                            btn.textContent = data.is_active ? 'Active' : 'Inactive';
                            btn.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold transition ' +
                                (data.is_active
                                    ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-700'
                                    : 'bg-red-100 text-red-700 hover:bg-green-100 hover:text-green-700');
                        }
                    });
            }
        </script>
    @endpush
</x-admin-layout>