{{-- resources/views/modules/audit-logs/index.blade.php --}}

<x-admin.app>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="w-full border-2 min-h-screen p-4 bg-white" x-data="auditLogs()" x-init="init()">

                {{-- ── Page Header ──────────────────────────────────────────── --}}
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Audit Logs
                        </h4>
                        <p class="text-xs text-gray-500 mt-0.5">Track all system activities, user actions, and changes.
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="loadStats()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Stats
                        </button>
                        <a :href="exportUrl()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg border border-green-400 text-green-700 hover:bg-green-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export CSV
                        </a>
                        <button @click="showPurgeModal = true"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg border border-red-400 text-red-600 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Purge
                        </button>
                    </div>
                </div>

                {{-- ── Stats Cards ──────────────────────────────────────────── --}}
                <div x-show="stats" x-transition class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    <div class="bg-white rounded-xl border p-3 text-center shadow-sm">
                        <div class="text-2xl font-bold text-logo-teal" x-text="stats ? stats.total : '—'"></div>
                        <div class="text-xs text-gray-500 mt-0.5">Total Logs</div>
                    </div>
                    <div class="bg-white rounded-xl border p-3 text-center shadow-sm">
                        <div class="text-2xl font-bold text-blue-500" x-text="stats ? stats.today : '—'"></div>
                        <div class="text-xs text-gray-500 mt-0.5">Today</div>
                    </div>
                    <div class="bg-white rounded-xl border p-3 text-center shadow-sm">
                        <div class="text-2xl font-bold text-red-500" x-text="stats ? stats.failed_today : '—'"></div>
                        <div class="text-xs text-gray-500 mt-0.5">Failed Today</div>
                    </div>
                    <div class="bg-white rounded-xl border p-3 text-center shadow-sm">
                        <div class="text-2xl font-bold text-green-600"
                            x-text="stats ? Object.keys(stats.by_module).length : '—'"></div>
                        <div class="text-xs text-gray-500 mt-0.5">Modules</div>
                    </div>
                </div>

                {{-- ── Filters ──────────────────────────────────────────────── --}}
                <div class="bg-gray-50 overflow-x-auto rounded-xl border p-3 mb-4">
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-6 gap-2">
                        <input type="text"
                            class="col-span-2 border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal"
                            placeholder="Search description, user, IP…" x-model="filters.search"
                            @input.debounce.400ms="fetchData()">

                        <select
                            class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal"
                            x-model="filters.module" @change="fetchData()">
                            <option value="">All Modules</option>
                            <option value="BPLS">BPLS</option>
                            <option value="RPTA">RPTA</option>
                            <option value="Settings">Settings</option>
                            <option value="Auth">Auth</option>
                            <option value="OR Assignment">OR Assignment</option>
                        </select>

                        <select
                            class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal"
                            x-model="filters.action" @change="fetchData()">
                            <option value="">All Actions</option>
                            <option value="created">Created</option>
                            <option value="updated">Updated</option>
                            <option value="deleted">Deleted</option>
                            <option value="viewed">Viewed</option>
                            <option value="payment">Payment</option>
                            <option value="status_change">Status Change</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="export">Export</option>
                        </select>

                        <select
                            class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal"
                            x-model="filters.status" @change="fetchData()">
                            <option value="">All Status</option>
                            <option value="success">Success</option>
                            <option value="failed">Failed</option>
                            <option value="warning">Warning</option>
                        </select>

                        <div class="flex gap-1 flex-col">
                            <input type="date"
                                class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal"
                                x-model="filters.date_from" @change="fetchData()" title="From">
                            <input type="date"
                                class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal"
                                x-model="filters.date_to" @change="fetchData()" title="To">
                        </div>
                    </div>
                </div>

                {{-- ── Table ────────────────────────────────────────────────── --}}
                <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

                    {{-- Table header bar --}}
                    <div class="flex items-center justify-between px-4 py-2 border-b bg-gray-50">
                        <span class="text-sm font-semibold text-gray-700">
                            Log Entries
                            <span class="ml-1 bg-logo-teal text-white text-xs font-bold px-2 py-0.5 rounded-full"
                                x-text="totalRecords"></span>
                        </span>
                        <select class="border border-gray-300 rounded-lg px-2 py-1 text-xs bg-white focus:outline-none"
                            x-model="perPage" @change="fetchData()">
                            <option value="25">25 / page</option>
                            <option value="50">50 / page</option>
                            <option value="100">100 / page</option>
                        </select>
                    </div>

                    {{-- Loading spinner --}}
                    <div x-show="loading" class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <svg class="animate-spin h-8 w-8 mb-2 text-logo-teal" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <span class="text-sm">Loading…</span>
                    </div>

                    {{-- Empty state --}}
                    <div x-show="!loading && logs.length === 0"
                        class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="text-sm">No audit logs found.</span>
                    </div>

                    {{-- Data table --}}
                    <div x-show="!loading && logs.length > 0" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b text-xs text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 text-left cursor-pointer hover:text-logo-teal select-none"
                                        @click="toggleSort('created_at')">
                                        Date/Time
                                        <span
                                            x-text="sortBy === 'created_at' ? (sortDir === 'asc' ? '↑' : '↓') : '↕'"></span>
                                    </th>
                                    <th class="px-4 py-3 text-left cursor-pointer hover:text-logo-teal select-none"
                                        @click="toggleSort('user_name')">
                                        User
                                        <span
                                            x-text="sortBy === 'user_name' ? (sortDir === 'asc' ? '↑' : '↓') : '↕'"></span>
                                    </th>
                                    <th class="px-4 py-3 text-left">Module</th>
                                    <th class="px-4 py-3 text-left">Action</th>
                                    <th class="px-4 py-3 text-left">Description</th>
                                    <th class="px-4 py-3 text-left">IP</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="log in logs" :key="log.id">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap"
                                            x-text="formatDate(log.created_at)"></td>
                                        <td class="px-4 py-3 font-medium text-gray-800"
                                            x-text="log.user_name ? log.user_name : '—'"></td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border"
                                                x-text="log.module"></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span :class="actionBadge(log.action)"
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                                x-text="log.action"></span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate"
                                            x-text="log.description"></td>
                                        <td class="px-4 py-3 text-xs text-gray-400"
                                            x-text="log.ip_address ? log.ip_address : '—'"></td>
                                        <td class="px-4 py-3">
                                            <span :class="statusBadge(log.status)"
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                                x-text="log.status"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button @click="viewDetail(log)"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-logo-teal text-logo-teal hover:bg-logo-teal hover:text-white transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div x-show="!loading && logs.length > 0"
                        class="flex items-center justify-between px-4 py-2 border-t bg-gray-50 text-xs text-gray-500">
                        <span>
                            Showing
                            <span x-text="((currentPage - 1) * perPage) + 1"></span>–<span
                                x-text="Math.min(currentPage * perPage, totalRecords)"></span>
                            of <span x-text="totalRecords"></span>
                        </span>
                        <div class="flex gap-1">
                            <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1"
                                class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                ‹ Prev
                            </button>
                            <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage"
                                class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                Next ›
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Detail Modal ─────────────────────────────────────────── --}}
                <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;"
                    @click.self="showDetailModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-screen overflow-y-auto">
                        <div class="flex items-center justify-between px-6 py-4 border-b">
                            <h5 class="font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Log Detail
                                <span class="text-gray-400 font-normal text-sm"
                                    x-text="selected ? '#' + selected.id : ''"></span>
                            </h5>
                            <button @click="showDetailModal = false"
                                class="text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="px-6 py-4 grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">Date / Time</div>
                                <div x-text="selected ? formatDate(selected.created_at) : '—'"></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">User</div>
                                <div x-text="selected ? (selected.user_name ? selected.user_name : '—') : '—'"></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">Module</div>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 border"
                                    x-text="selected ? selected.module : '—'"></span>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">Action</div>
                                <span :class="selected ? actionBadge(selected.action) : ''"
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                    x-text="selected ? selected.action : '—'"></span>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">Status</div>
                                <span :class="selected ? statusBadge(selected.status) : ''"
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                    x-text="selected ? selected.status : '—'"></span>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">IP / Method</div>
                                <div
                                    x-text="selected ? ((selected.ip_address ? selected.ip_address : '—') + ' · ' + (selected.method ? selected.method : '—')) : '—'">
                                </div>
                            </div>
                            <div class="col-span-2">
                                <div class="text-xs text-gray-400 mb-0.5">Description</div>
                                <div x-text="selected ? selected.description : '—'"></div>
                            </div>
                            <div class="col-span-2">
                                <div class="text-xs text-gray-400 mb-0.5">URL</div>
                                <div class="text-xs text-gray-500 break-all"
                                    x-text="selected ? (selected.url ? selected.url : '—') : '—'"></div>
                            </div>
                            <div class="col-span-2" x-show="selected && (selected.old_values || selected.new_values)">
                                <div class="text-xs text-gray-400 mb-1">Changes</div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-red-50 rounded-lg p-3">
                                        <div class="text-xs font-bold text-red-500 mb-1">Before</div>
                                        <pre class="text-xs whitespace-pre-wrap break-all"
                                            x-text="selected && selected.old_values ? JSON.stringify(selected.old_values, null, 2) : 'N/A'"></pre>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-3">
                                        <div class="text-xs font-bold text-green-600 mb-1">After</div>
                                        <pre class="text-xs whitespace-pre-wrap break-all"
                                            x-text="selected && selected.new_values ? JSON.stringify(selected.new_values, null, 2) : 'N/A'"></pre>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2" x-show="selected && selected.extra">
                                <div class="text-xs text-gray-400 mb-1">Extra Info</div>
                                <pre class="text-xs bg-gray-50 rounded-lg p-3 whitespace-pre-wrap break-all"
                                    x-text="selected && selected.extra ? JSON.stringify(selected.extra, null, 2) : ''"></pre>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t flex justify-end">
                            <button @click="showDetailModal = false"
                                class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                                Close
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Purge Modal ──────────────────────────────────────────── --}}
                <div x-show="showPurgeModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-start="opacity-100"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;"
                    @click.self="showPurgeModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                        <div class="flex items-center justify-between px-6 py-4 border-b">
                            <h5 class="font-bold text-red-600 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Purge Old Logs
                            </h5>
                            <button @click="showPurgeModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('audit-logs.purge') }}"
                            onsubmit="return confirm('This will permanently delete old logs. Continue?')">
                            @csrf
                            @method('DELETE')
                            <div class="px-6 py-4 space-y-3">
                                <div
                                    class="flex items-start gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                    </svg>
                                    This action is <strong class="mx-1">irreversible</strong>. Selected logs will be
                                    permanently deleted.
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Delete logs older
                                        than:</label>
                                    <select name="older_than_days"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-400">
                                        <option value="30">30 days</option>
                                        <option value="60">60 days</option>
                                        <option value="90" selected>90 days</option>
                                        <option value="180">180 days</option>
                                        <option value="365">1 year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="px-6 py-4 border-t flex justify-end gap-2">
                                <button type="button" @click="showPurgeModal = false"
                                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-semibold">
                                    Purge Logs
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>{{-- /x-data --}}
        </div>
    </div>

    @push('scripts')
        <script>
            function auditLogs() {
                return {
                    logs: [],
                    loading: false,
                    selected: null,
                    stats: null,
                    totalRecords: 0,
                    currentPage: 1,
                    lastPage: 1,
                    perPage: 25,
                    sortBy: 'created_at',
                    sortDir: 'desc',
                    showDetailModal: false,
                    showPurgeModal: false,

                    filters: {
                        search: '',
                        module: '',
                        action: '',
                        status: '',
                        date_from: '',
                        date_to: '',
                    },

                    init() {
                        this.fetchData();
                    },

                    fetchData(page) {
                        var self = this;
                        self.loading = true;
                        self.currentPage = page || 1;

                        var params = new URLSearchParams();
                        params.append('search', self.filters.search);
                        params.append('module', self.filters.module);
                        params.append('action', self.filters.action);
                        params.append('status', self.filters.status);
                        params.append('date_from', self.filters.date_from);
                        params.append('date_to', self.filters.date_to);
                        params.append('page', self.currentPage);
                        params.append('per_page', self.perPage);
                        params.append('sort_by', self.sortBy);
                        params.append('sort_dir', self.sortDir);

                        fetch('{{ route('audit-logs.data') }}?' + params.toString(), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(function(res) {
                                return res.json();
                            })
                            .then(function(json) {
                                self.logs = json.data || [];
                                self.totalRecords = json.total || 0;
                                self.lastPage = json.last_page || 1;
                                self.loading = false;
                            })
                            .catch(function(e) {
                                console.error('Audit log fetch error:', e);
                                self.loading = false;
                            });
                    },

                    goToPage(p) {
                        if (p >= 1 && p <= this.lastPage) {
                            this.fetchData(p);
                        }
                    },

                    toggleSort(col) {
                        if (this.sortBy === col) {
                            this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.sortBy = col;
                            this.sortDir = 'desc';
                        }
                        this.fetchData();
                    },

                    loadStats() {
                        var self = this;
                        fetch('{{ route('audit-logs.stats') }}', {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(function(res) {
                                return res.json();
                            })
                            .then(function(json) {
                                self.stats = json;
                            })
                            .catch(function(e) {
                                console.error(e);
                            });
                    },

                    viewDetail(log) {
                        this.selected = log;
                        this.showDetailModal = true;
                    },

                    exportUrl() {
                        var params = new URLSearchParams();
                        if (this.filters.module) params.append('module', this.filters.module);
                        if (this.filters.action) params.append('action', this.filters.action);
                        if (this.filters.status) params.append('status', this.filters.status);
                        if (this.filters.date_from) params.append('date_from', this.filters.date_from);
                        if (this.filters.date_to) params.append('date_to', this.filters.date_to);
                        return '{{ route('audit-logs.export') }}?' + params.toString();
                    },

                    formatDate(dt) {
                        if (!dt) return '—';
                        return new Date(dt).toLocaleString('en-PH', {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                        });
                    },

                    actionBadge(action) {
                        var map = {
                            'created': 'bg-green-100 text-green-700',
                            'updated': 'bg-blue-100 text-blue-700',
                            'deleted': 'bg-red-100 text-red-700',
                            'payment': 'bg-purple-100 text-purple-700',
                            'status_change': 'bg-yellow-100 text-yellow-700',
                            'login': 'bg-teal-100 text-teal-700',
                            'logout': 'bg-gray-100 text-gray-600',
                            'export': 'bg-indigo-100 text-indigo-700',
                            'viewed': 'bg-gray-100 text-gray-500',
                        };
                        return map[action] || 'bg-gray-100 text-gray-500';
                    },

                    statusBadge(status) {
                        var map = {
                            'success': 'bg-green-100 text-green-700',
                            'failed': 'bg-red-100 text-red-700',
                            'warning': 'bg-yellow-100 text-yellow-800',
                        };
                        return map[status] || 'bg-gray-100 text-gray-500';
                    },
                };
            }
        </script>
    @endpush

</x-admin.app>
