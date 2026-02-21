{{-- resources/views/modules/bpls/settings/index.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="w-full px-2 sm:px-4 lg:px-6">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-3 sm:p-4"
                x-data="bplsSettings()" x-init="init()">

                {{-- ── Page Header ── --}}
                <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-green tracking-tight">BPLS Settings</h1>
                        <p class="text-gray text-xs sm:text-sm mt-0.5">Manage system configuration and accountable forms
                        </p>
                    </div>
                    <span
                        class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20 self-start sm:self-auto shrink-0">
                        GReAT System
                    </span>
                </div>

                {{-- ── Settings Tab Nav ── --}}
                <div class="flex gap-1 mb-5 bg-white rounded-2xl p-1.5 shadow-sm border border-lumot/20 overflow-x-auto">
                    <button @click="activeTab = 'or'"
                        :class="activeTab === 'or'
                            ?
                            'bg-logo-teal text-white shadow-md' :
                            'text-gray hover:bg-lumot/20'"
                        class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Accountable Form Assignment
                    </button>
                    {{-- Placeholder tabs for future sections --}}
                    <button @click="activeTab = 'lob'"
                        :class="activeTab === 'lob'
                            ?
                            'bg-logo-teal text-white shadow-md' :
                            'text-gray hover:bg-lumot/20'"
                        class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Line of Business
                    </button>
                    <button @click="activeTab = 'tax'"
                        :class="activeTab === 'tax'
                            ?
                            'bg-logo-teal text-white shadow-md' :
                            'text-gray hover:bg-lumot/20'"
                        class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7 7h.01M7 3h5l4.5 4.5-10 10-4.5-4.5L7 3z" />
                        </svg>
                        Tax Category
                    </button>
                </div>

                {{-- ════════════════════════════════════════════════════════ --}}
                {{-- TAB: ACCOUNTABLE FORM (OR) ASSIGNMENT                    --}}
                {{-- ════════════════════════════════════════════════════════ --}}
                <div x-show="activeTab === 'or'" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0">

                    <div class="flex flex-col xl:flex-row gap-5 items-start">

                        {{-- ── Left: Assignment Form ── --}}
                        <div class="w-full xl:w-80 shrink-0">
                            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                                <div
                                    class="bg-gradient-to-r from-logo-teal to-logo-blue px-4 py-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <p class="text-xs font-extrabold text-white uppercase tracking-wide"
                                        x-text="editMode ? 'Edit O.R. Assignment' : 'Assign O.R. Number for Cashier'">
                                    </p>
                                </div>

                                <div class="p-4 space-y-4">

                                    {{-- Start / End OR --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="block text-[10px] font-extrabold text-red-500 uppercase mb-1.5">
                                                * Start O.R. Number
                                            </label>
                                            <input type="text" x-model="form.start_or" placeholder="ENTER NUMBER"
                                                :class="errors.start_or ? 'border-red-300 ring-2 ring-red-100' :
                                                    'border-lumot/30'"
                                                class="w-full text-xs border rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 font-mono uppercase tracking-wider">
                                            <p x-show="errors.start_or" class="text-[10px] text-red-500 mt-1"
                                                x-text="errors.start_or"></p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-extrabold text-red-500 uppercase mb-1.5">
                                                * End O.R. Number
                                            </label>
                                            <input type="text" x-model="form.end_or" placeholder="ENTER NUMBER"
                                                :class="errors.end_or ? 'border-red-300 ring-2 ring-red-100' : 'border-lumot/30'"
                                                class="w-full text-xs border rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 font-mono uppercase tracking-wider">
                                            <p x-show="errors.end_or" class="text-[10px] text-red-500 mt-1"
                                                x-text="errors.end_or"></p>
                                        </div>
                                    </div>

                                    {{-- Name of Cashier --}}
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-red-500 uppercase mb-1.5">
                                            * Name of Cashier
                                        </label>
                                        <select x-model="form.user_id"
                                            :class="errors.user_id ? 'border-red-300 ring-2 ring-red-100' : 'border-lumot/30'"
                                            class="w-full text-xs border rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">— Select Cashier —</option>
                                            @foreach ($cashiers as $cashier)
                                                <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                                            @endforeach
                                        </select>
                                        <p x-show="errors.user_id" class="text-[10px] text-red-500 mt-1"
                                            x-text="errors.user_id"></p>
                                    </div>

                                    {{-- Type of Receipt --}}
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-red-500 uppercase mb-1.5">
                                            * Type of Receipt
                                        </label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <template x-for="type in receiptTypes" :key="type.value">
                                                <button type="button" @click="form.receipt_type = type.value"
                                                    :class="form.receipt_type === type.value ?
                                                        'bg-logo-teal text-white border-logo-teal shadow-md shadow-logo-teal/20' :
                                                        'bg-white text-gray border-lumot/30 hover:border-logo-teal/50 hover:text-logo-teal'"
                                                    class="flex flex-col items-center gap-0.5 px-2 py-2.5 border rounded-xl text-[10px] font-extrabold transition-all duration-150 uppercase tracking-wide">
                                                    <span x-text="type.value" class="text-sm font-extrabold"></span>
                                                    <span x-text="type.sub"
                                                        class="opacity-70 text-center leading-tight"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <p x-show="errors.receipt_type" class="text-[10px] text-red-500 mt-1"
                                            x-text="errors.receipt_type"></p>
                                    </div>

                                    {{-- Selected type preview --}}
                                    <div x-show="form.receipt_type"
                                        class="flex items-center gap-2 p-2.5 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                                        <div
                                            class="w-6 h-6 rounded-lg bg-logo-teal/10 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] text-gray/50 font-semibold">Selected Type</p>
                                            <p class="text-xs font-bold text-logo-teal"
                                                x-text="receiptTypes.find(t => t.value === form.receipt_type)?.label ?? ''">
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Success flash --}}
                                    <div x-show="successMsg" x-cloak x-transition
                                        class="flex items-center gap-2 p-2.5 bg-logo-green/10 border border-logo-green/30 rounded-xl">
                                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <p class="text-xs font-bold text-logo-green" x-text="successMsg"></p>
                                    </div>

                                    {{-- Error flash --}}
                                    <div x-show="errorMsg" x-cloak x-transition
                                        class="flex items-center gap-2 p-2.5 bg-red-50 border border-red-200 rounded-xl">
                                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-xs font-semibold text-red-500" x-text="errorMsg"></p>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex gap-2 pt-1">
                                        <button @click="submit()" :disabled="saving"
                                            class="flex-1 flex items-center justify-center gap-2 py-3 font-extrabold text-sm rounded-xl transition-all duration-200 shadow-md disabled:opacity-60"
                                            :class="editMode
                                                ?
                                                'bg-logo-blue text-white hover:bg-green shadow-logo-blue/20' :
                                                'bg-logo-teal text-white hover:bg-green shadow-logo-teal/20'">
                                            <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v8z" />
                                            </svg>
                                            <svg x-show="!saving && !editMode" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <svg x-show="!saving && editMode" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span
                                                x-text="saving ? 'Saving...' : (editMode ? 'Update Assignment' : 'Assign')"></span>
                                        </button>
                                        <button x-show="editMode" @click="cancelEdit()"
                                            class="px-4 py-3 bg-lumot/20 text-gray text-xs font-bold rounded-xl hover:bg-lumot/40 transition-colors">
                                            Cancel
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ── Right: OR Assignment List ── --}}
                        <div class="flex-1 min-w-0 w-full">
                            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">

                                {{-- List Header --}}
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-lumot/20">
                                    <div>
                                        <p class="text-sm font-extrabold text-green">List of Assign O.R.</p>
                                        <p class="text-[10px] text-gray/50 mt-0.5"
                                            x-text="total + ' total record(s)'"></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray/60 shrink-0">Show</span>
                                        <select x-model.number="perPage" @change="loadAssignments(1)"
                                            class="text-xs border border-lumot/30 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                        <span class="text-xs text-gray/60 shrink-0">entries</span>
                                        <div class="relative ml-2">
                                            <input type="text" x-model.debounce.400ms="search"
                                                @input="loadAssignments(1)" placeholder="Search..."
                                                class="text-xs border border-lumot/30 rounded-xl pl-8 pr-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 w-40">
                                            <svg class="w-3.5 h-3.5 text-gray/40 absolute left-2.5 top-1/2 -translate-y-1/2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Loading --}}
                                <div x-show="loading" class="p-8 text-center">
                                    <svg class="w-6 h-6 text-logo-teal animate-spin mx-auto" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                    </svg>
                                    <p class="text-xs text-gray/50 mt-2">Loading assignments...</p>
                                </div>

                                {{-- Table --}}
                                <div x-show="!loading" style="overflow: auto; -webkit-overflow-scrolling: touch;">
                                    <table class="w-full text-xs border-collapse" style="min-width: 640px;">
                                        <thead>
                                            <tr class="bg-logo-teal text-white">
                                                <th
                                                    class="text-left px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide whitespace-nowrap">
                                                    Start O.R.
                                                    <svg class="w-2.5 h-2.5 inline ml-0.5 opacity-60" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                </th>
                                                <th
                                                    class="text-left px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide whitespace-nowrap">
                                                    End O.R.
                                                    <svg class="w-2.5 h-2.5 inline ml-0.5 opacity-60" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                </th>
                                                <th
                                                    class="text-left px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide whitespace-nowrap">
                                                    Receipt Type
                                                    <svg class="w-2.5 h-2.5 inline ml-0.5 opacity-60" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                </th>
                                                <th
                                                    class="text-left px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide whitespace-nowrap">
                                                    Cashier Name
                                                    <svg class="w-2.5 h-2.5 inline ml-0.5 opacity-60" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                </th>
                                                <th
                                                    class="text-center px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide whitespace-nowrap">
                                                    Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-if="assignments.length === 0">
                                                <tr>
                                                    <td colspan="5"
                                                        class="text-center px-4 py-10 text-gray/40 text-xs">
                                                        No assignments found. Use the form on the left to add one.
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-for="(row, i) in assignments" :key="row.id">
                                                <tr class="border-b border-lumot/10 transition-colors"
                                                    :class="editingId === row.id ?
                                                        'bg-logo-teal/5 border-l-2 border-l-logo-teal' :
                                                        (i % 2 === 0 ? 'bg-white hover:bg-bluebody/30' :
                                                            'bg-bluebody/20 hover:bg-bluebody/40')">
                                                    <td class="px-4 py-3 font-mono font-bold text-green whitespace-nowrap"
                                                        x-text="row.start_or"></td>
                                                    <td class="px-4 py-3 font-mono font-bold text-green whitespace-nowrap"
                                                        x-text="row.end_or"></td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span
                                                            class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase"
                                                            :class="{
                                                                'bg-logo-teal/10 text-logo-teal': row
                                                                    .receipt_type === '51C',
                                                                'bg-indigo-50 text-indigo-600': row
                                                                    .receipt_type === 'RPTA',
                                                                'bg-amber-50 text-amber-600': row
                                                                    .receipt_type === 'CTC',
                                                            }"
                                                            x-text="row.receipt_label"></span>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray whitespace-nowrap"
                                                        x-text="row.cashier_name"></td>
                                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                                        <div class="flex items-center justify-center gap-1.5">
                                                            <button @click="startEdit(row)"
                                                                class="flex items-center gap-1 px-3 py-1.5 bg-amber-400 hover:bg-amber-500 text-white text-[10px] font-extrabold rounded-lg transition-colors shadow-sm">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2.5">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Edit
                                                            </button>
                                                            <button @click="confirmDelete(row)"
                                                                class="flex items-center gap-1 px-3 py-1.5 bg-red-400 hover:bg-red-500 text-white text-[10px] font-extrabold rounded-lg transition-colors shadow-sm">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2.5">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                <div x-show="!loading && lastPage > 1"
                                    class="flex items-center justify-between px-4 py-3 border-t border-lumot/20">
                                    <p class="text-[10px] text-gray/50"
                                        x-text="'Showing page ' + currentPage + ' of ' + lastPage"></p>
                                    <div class="flex gap-1">
                                        <button @click="loadAssignments(currentPage - 1)" :disabled="currentPage <= 1"
                                            class="px-3 py-1.5 text-xs border border-lumot/30 rounded-lg disabled:opacity-40 hover:bg-lumot/20 transition-colors">
                                            ‹ Prev
                                        </button>
                                        <template x-for="p in pageRange" :key="p">
                                            <button @click="loadAssignments(p)"
                                                :class="p === currentPage ?
                                                    'bg-logo-teal text-white border-logo-teal' :
                                                    'border-lumot/30 hover:bg-lumot/20'"
                                                class="px-3 py-1.5 text-xs border rounded-lg transition-colors"
                                                x-text="p"></button>
                                        </template>
                                        <button @click="loadAssignments(currentPage + 1)"
                                            :disabled="currentPage >= lastPage"
                                            class="px-3 py-1.5 text-xs border border-lumot/30 rounded-lg disabled:opacity-40 hover:bg-lumot/20 transition-colors">
                                            Next ›
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>{{-- end OR tab --}}

                {{-- ════════════════════════════════════════════════════════ --}}
                {{-- OTHER TABS — placeholder                                 --}}
                {{-- ════════════════════════════════════════════════════════ --}}
                <div x-show="activeTab === 'lob'" x-cloak>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-12 text-center">
                        <div class="w-14 h-14 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-gray/30" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray">Line of Business</p>
                        <p class="text-xs text-gray/40 mt-1">Coming soon — manage BPLS line of business categories
                            here.</p>
                    </div>
                </div>

                <div x-show="activeTab === 'tax'" x-cloak>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-12 text-center">
                        <div class="w-14 h-14 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-gray/30" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 7h.01M7 3h5l4.5 4.5-10 10-4.5-4.5L7 3z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray">Tax Category</p>
                        <p class="text-xs text-gray/40 mt-1">Coming soon — configure tax categories for BPLS here.</p>
                    </div>
                </div>

            </div>{{-- end main --}}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- DELETE CONFIRM MODAL                                               --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div x-data x-show="$store.deleteModal.open" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="$store.deleteModal.cancel()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 p-6 max-w-sm w-full"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-green">Delete Assignment?</p>
                    <p class="text-xs text-gray/60"
                        x-text="'O.R. ' + ($store.deleteModal.row?.start_or ?? '') + ' – ' + ($store.deleteModal.row?.end_or ?? '')">
                    </p>
                </div>
            </div>
            <p class="text-xs text-gray mb-5">This action cannot be undone. The O.R. assignment will be permanently
                removed.</p>
            <div class="flex gap-2">
                <button @click="$store.deleteModal.cancel()"
                    class="flex-1 py-2.5 bg-lumot/20 text-gray text-xs font-bold rounded-xl hover:bg-lumot/40 transition-colors">
                    Cancel
                </button>
                <button @click="$store.deleteModal.confirm()"
                    class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-xl transition-colors shadow-md shadow-red-500/20">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ── Shared delete modal store ─────────────────────────────────────
            document.addEventListener('alpine:init', () => {
                Alpine.store('deleteModal', {
                    open: false,
                    row: null,
                    _resolve: null,

                    show(row) {
                        this.row = row;
                        this.open = true;
                        return new Promise(res => {
                            this._resolve = res;
                        });
                    },
                    confirm() {
                        this.open = false;
                        this._resolve?.(true);
                    },
                    cancel() {
                        this.open = false;
                        this._resolve?.(false);
                    },
                });
            });

            function bplsSettings() {
                return {
                    activeTab: 'or',

                    // ── Form state ─────────────────────────────────────────────
                    form: {
                        start_or: '',
                        end_or: '',
                        user_id: '',
                        receipt_type: '',
                    },
                    errors: {},
                    saving: false,
                    editMode: false,
                    editingId: null,
                    successMsg: '',
                    errorMsg: '',

                    // ── List state ─────────────────────────────────────────────
                    assignments: [],
                    loading: true,
                    total: 0,
                    currentPage: 1,
                    lastPage: 1,
                    perPage: 10,
                    search: '',

                    // ── Receipt types ──────────────────────────────────────────
                    receiptTypes: [{
                            value: '51C',
                            label: '51C (Miscellaneous)',
                            sub: 'Misc'
                        },
                        {
                            value: 'RPTA',
                            label: '56 (RPTA)',
                            sub: 'RPTA'
                        },
                        {
                            value: 'CTC',
                            label: 'CTC (Community Tax)',
                            sub: 'CTC'
                        },
                    ],

                    get pageRange() {
                        const range = [];
                        const start = Math.max(1, this.currentPage - 2);
                        const end = Math.min(this.lastPage, this.currentPage + 2);
                        for (let p = start; p <= end; p++) range.push(p);
                        return range;
                    },

                    init() {
                        this.loadAssignments(1);
                    },

                    // ── Load list ──────────────────────────────────────────────
                    async loadAssignments(page = 1) {
                        this.loading = true;
                        try {
                            const params = new URLSearchParams({
                                page,
                                per_page: this.perPage,
                                ...(this.search ? {
                                    search: this.search
                                } : {}),
                            });
                            const res = await fetch(`{{ route('bpls.settings.or-assignments.index') }}?${params}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();

                            this.assignments = data.data;
                            this.total = data.total;
                            this.currentPage = data.current_page;
                            this.lastPage = data.last_page;
                            this.perPage = data.per_page;
                        } catch (e) {
                            console.error('Load failed', e);
                        } finally {
                            this.loading = false;
                        }
                    },

                    // ── Submit (create or update) ──────────────────────────────
                    async submit() {
                        this.errors = {};
                        this.successMsg = '';
                        this.errorMsg = '';

                        // Basic client-side validation
                        if (!this.form.start_or.trim()) this.errors.start_or = 'Start O.R. is required';
                        if (!this.form.end_or.trim()) this.errors.end_or = 'End O.R. is required';
                        if (!this.form.user_id) this.errors.user_id = 'Please select a cashier';
                        if (!this.form.receipt_type) this.errors.receipt_type = 'Please select a receipt type';
                        if (Object.keys(this.errors).length) return;

                        this.saving = true;
                        try {
                            const isEdit = this.editMode && this.editingId;
                            const url = isEdit ?
                                `/bpls/settings/or-assignments/${this.editingId}` :
                                `{{ route('bpls.settings.or-assignments.store') }}`;
                            const method = isEdit ? 'PUT' : 'POST';

                            const res = await fetch(url, {
                                method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ??
                                        '',
                                },
                                body: JSON.stringify(this.form),
                            });
                            const data = await res.json();

                            if (!data.success) throw new Error(data.message ?? 'Save failed');

                            this.successMsg = isEdit ? 'Assignment updated successfully!' : 'O.R. assigned successfully!';
                            this.resetForm();
                            this.loadAssignments(1);
                            setTimeout(() => {
                                this.successMsg = '';
                            }, 3500);
                        } catch (e) {
                            this.errorMsg = e.message;
                            setTimeout(() => {
                                this.errorMsg = '';
                            }, 4000);
                        } finally {
                            this.saving = false;
                        }
                    },

                    // ── Edit ───────────────────────────────────────────────────
                    startEdit(row) {
                        this.editMode = true;
                        this.editingId = row.id;
                        this.form.start_or = row.start_or;
                        this.form.end_or = row.end_or;
                        this.form.receipt_type = row.receipt_type;
                        this.form.user_id = String(row.user_id);
                        this.errors = {};
                        this.successMsg = '';
                        this.errorMsg = '';
                        // Scroll form into view on mobile
                        this.$el.querySelector('input[x-model="form.start_or"]')?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    },

                    cancelEdit() {
                        this.editMode = false;
                        this.editingId = null;
                        this.resetForm();
                    },

                    // ── Delete ─────────────────────────────────────────────────
                    async confirmDelete(row) {
                        const confirmed = await this.$store.deleteModal.show(row);
                        if (!confirmed) return;

                        try {
                            const res = await fetch(`/bpls/settings/or-assignments/${row.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ??
                                        '',
                                },
                            });
                            const data = await res.json();
                            if (!data.success) throw new Error(data.message);

                            this.successMsg = 'Assignment deleted.';
                            this.loadAssignments(this.currentPage);
                            setTimeout(() => {
                                this.successMsg = '';
                            }, 3000);
                        } catch (e) {
                            this.errorMsg = e.message;
                            setTimeout(() => {
                                this.errorMsg = '';
                            }, 4000);
                        }
                    },

                    resetForm() {
                        this.form = {
                            start_or: '',
                            end_or: '',
                            user_id: '',
                            receipt_type: ''
                        };
                        this.errors = {};
                        this.editMode = false;
                        this.editingId = null;
                    },
                };
            }
        </script>
    @endpush
</x-admin.app>
