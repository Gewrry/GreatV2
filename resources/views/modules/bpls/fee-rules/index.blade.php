{{-- resources/views/modules/bpls/fee-rules/index.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4" x-data="feeRulesManager()"
                x-init="init()">

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- RULE FORM MODAL — Create / Edit                            --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                    <div class="absolute inset-0 bg-green/40 backdrop-blur-sm" @click="closeModal()"></div>

                    <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-2xl max-h-[92vh] flex flex-col"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2">

                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20 shrink-0">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-green"
                                        x-text="modal.editing ? 'Edit Fee Rule' : 'New Fee Rule'"></h3>
                                    <p class="text-[11px] text-gray">Configure tax / fee computation</p>
                                </div>
                            </div>
                            <button @click="closeModal()"
                                class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="overflow-y-auto flex-1 p-5 space-y-4">

                            {{-- Name --}}
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Fee / Tax Name *</label>
                                <input type="text" x-model="modal.form.name"
                                    placeholder="e.g. Gross Sales Tax, Mayor's Permit..."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                            </div>

                            {{-- Formula Type --}}
                            <div>
                                <label class="block text-xs font-bold text-gray mb-2">Formula Type *</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="ft in formulaTypes" :key="ft.value">
                                        <label class="cursor-pointer">
                                            <input type="radio" :value="ft.value"
                                                x-model="modal.form.formula_type" @change="onFormulaTypeChange()"
                                                class="peer hidden">
                                            <div
                                                class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal
                                                border-2 border-lumot/30 rounded-xl px-3 py-2.5 text-center transition-all duration-150
                                                hover:border-logo-teal/50 hover:bg-logo-teal/5 select-none">
                                                <p class="text-[11px] font-extrabold" x-text="ft.label"></p>
                                                <p class="text-[9px] opacity-70 mt-0.5" x-text="ft.sub"></p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            {{-- ── FLAT AMOUNT ── --}}
                            <div x-show="modal.form.formula_type === 'flat_amount'"
                                class="bg-bluebody/60 rounded-xl p-4 border border-lumot/20">
                                <label class="block text-xs font-bold text-gray mb-1.5">Fixed Amount (₱)</label>
                                <div class="relative max-w-xs">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray/50 font-semibold">₱</span>
                                    <input type="number" min="0" step="0.01" x-model="modal.form.flat_amount"
                                        class="w-full pl-7 pr-3 py-2.5 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-mono">
                                </div>
                            </div>

                            {{-- ── PERCENTAGE ── --}}
                            <div x-show="modal.form.formula_type === 'percentage'"
                                class="bg-bluebody/60 rounded-xl p-4 border border-lumot/20">
                                <label class="block text-xs font-bold text-gray mb-1.5">Percentage of Gross Sales
                                    (%)</label>
                                <div class="relative max-w-xs">
                                    <input type="number" min="0" step="0.001" max="100"
                                        x-model="modal.form.percentage"
                                        class="w-full pl-3 pr-8 py-2.5 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-mono">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray/50 font-semibold">%</span>
                                </div>
                            </div>

                            {{-- ── SCALE TABLE ── --}}
                            <div x-show="modal.form.formula_type === 'scale_table'"
                                class="bg-bluebody/60 rounded-xl p-4 border border-lumot/20 space-y-2">
                                <label class="block text-xs font-bold text-gray mb-2">Amount per Business Scale</label>
                                <template x-for="s in scales" :key="s.code">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[11px] text-gray/70 w-44 shrink-0" x-text="s.label"></span>
                                        <div class="relative flex-1">
                                            <span
                                                class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray/50 font-semibold">₱</span>
                                            <input type="number" min="0"
                                                :value="modal.form.scale_table[s.code] ?? 0"
                                                @input="modal.form.scale_table[s.code] = Number($event.target.value)"
                                                class="w-full pl-6 pr-3 py-2 text-xs border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-mono bg-white">
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- ── GRADUATED RATE ── --}}
                            <div x-show="modal.form.formula_type === 'graduated_rate'"
                                class="bg-bluebody/60 rounded-xl p-4 border border-lumot/20">
                                <label class="block text-xs font-bold text-gray mb-3">Rate Brackets (applied to Gross
                                    Sales)</label>
                                <div class="grid grid-cols-[1fr_1fr_32px] gap-1.5 mb-2">
                                    <span class="text-[10px] font-extrabold text-gray/60 uppercase px-1">Max Gross
                                        Sales (₱)</span>
                                    <span class="text-[10px] font-extrabold text-gray/60 uppercase px-1">Rate
                                        (decimal)</span>
                                    <span></span>
                                </div>
                                <template x-for="(row, i) in modal.form.rate_table" :key="i">
                                    <div class="grid grid-cols-[1fr_1fr_32px] gap-1.5 mb-1.5 items-center">
                                        <input type="text"
                                            :placeholder="i === modal.form.rate_table.length - 1 ? '∞ leave blank' : 'e.g. 500000'"
                                            :value="row.max ?? ''"
                                            @input="row.max = $event.target.value === '' ? null : Number($event.target.value)"
                                            class="text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-mono bg-white">
                                        <input type="number" step="0.0001" placeholder="e.g. 0.018"
                                            x-model="row.rate"
                                            class="text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-mono bg-white">
                                        <button @click="removeRateBracket(i)"
                                            class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors text-base font-bold">×</button>
                                    </div>
                                </template>
                                <button @click="addRateBracket()"
                                    class="mt-2 text-xs font-bold text-logo-teal hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Bracket
                                </button>
                                <p class="text-[10px] text-gray/40 mt-2">Last row with blank Max acts as catch-all (∞).
                                </p>
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Notes / Memo Reference</label>
                                <textarea x-model="modal.form.notes" rows="2" placeholder="e.g. Section 3B of Municipal Revenue Code 2025..."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 resize-none"></textarea>
                            </div>

                            {{-- Enabled toggle --}}
                            <label
                                class="flex items-center gap-3 cursor-pointer p-3 bg-bluebody/40 rounded-xl border border-lumot/20">
                                <div @click="modal.form.enabled = !modal.form.enabled"
                                    :class="modal.form.enabled ? 'bg-logo-teal' : 'bg-lumot/40'"
                                    class="w-10 h-5 rounded-full relative transition-colors shrink-0 cursor-pointer">
                                    <span :class="modal.form.enabled ? 'left-5' : 'left-0.5'"
                                        class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-all duration-200"></span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray">Active Rule</p>
                                    <p class="text-[10px] text-gray/50">Disabled rules are excluded from fee
                                        computation.</p>
                                </div>
                            </label>

                            {{-- Modal error --}}
                            <div x-show="modal.error" x-cloak
                                class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                                <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-semibold text-red-500" x-text="modal.error"></span>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div
                            class="flex items-center justify-between gap-2 px-5 py-4 border-t border-lumot/20 shrink-0">
                            <button @click="closeModal()"
                                class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                                Cancel
                            </button>
                            <button @click="saveRule()" :disabled="modal.saving || !modal.form.name.trim()"
                                class="px-5 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="modal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <span
                                    x-text="modal.saving ? 'Saving...' : (modal.editing ? 'Save Changes' : 'Add Rule')"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- DELETE CONFIRM MODAL                                        --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="deleteModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100">
                    <div class="absolute inset-0 bg-green/40 backdrop-blur-sm" @click="deleteModal.open = false">
                    </div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-red-100 w-full max-w-sm p-6 text-center"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                        <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-extrabold text-green mb-1">Delete Fee Rule?</h3>
                        <p class="text-xs text-gray mb-5">
                            "<span class="font-bold text-green" x-text="deleteModal.rule?.name"></span>"
                            will be permanently removed from the computation engine.
                        </p>
                        <div class="flex gap-2 justify-center">
                            <button @click="deleteModal.open = false"
                                class="px-4 py-2 text-sm font-bold text-gray bg-lumot/20 rounded-xl hover:bg-lumot/40 transition-colors">
                                Cancel
                            </button>
                            <button @click="confirmDelete()" :disabled="deleteModal.deleting"
                                class="px-4 py-2 text-sm font-bold text-white bg-red-500 rounded-xl hover:bg-red-600 transition-colors flex items-center gap-2">
                                <svg x-show="deleteModal.deleting" class="w-3.5 h-3.5 animate-spin" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <span x-text="deleteModal.deleting ? 'Deleting...' : 'Delete'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Page Header ── --}}
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">Fee Rules Manager</h1>
                        <p class="text-gray text-sm mt-0.5">Configure BPLS tax &amp; fee computation rules — BPLS 2026
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="resetDefaults()" :disabled="resetting"
                            class="flex items-center gap-1.5 px-4 py-2 bg-white text-gray border border-lumot/30 text-xs font-bold rounded-xl hover:bg-lumot/10 transition-colors">
                            <svg x-show="resetting" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <svg x-show="!resetting" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset to Defaults
                        </button>
                        <button @click="openCreateModal()"
                            class="flex items-center gap-1.5 px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            New Fee Rule
                        </button>
                    </div>
                </div>

                {{-- ── Stat Pills ── --}}
                <div class="grid grid-cols-4 gap-3 mb-5">
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Total Rules</p>
                            <p class="text-lg font-extrabold text-green" x-text="rules.length">0</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-green/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Active</p>
                            <p class="text-lg font-extrabold text-green" x-text="rules.filter(r => r.enabled).length">
                                0</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-yellow/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Disabled</p>
                            <p class="text-lg font-extrabold text-green"
                                x-text="rules.filter(r => !r.enabled).length">0</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 7h.01M7 3h5l4.586 4.586a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-4-4a2 2 0 010-2.828L7 7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Formula Types</p>
                            <p class="text-lg font-extrabold text-green"
                                x-text="new Set(rules.map(r => r.formula_type)).size">0</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

                    {{-- ── Rules List ── --}}
                    <div>
                        {{-- Fetch error banner --}}
                        <div x-show="fetchError" x-cloak
                            class="mb-3 flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                            <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-red-600">Failed to load fee rules</p>
                                <p class="text-[11px] text-red-500 mt-0.5 font-mono break-all" x-text="fetchError">
                                </p>
                                <button @click="fetchRules()"
                                    class="mt-1.5 text-xs font-bold text-red-600 hover:underline">↺ Try again</button>
                            </div>
                        </div>

                        {{-- Reset error banner --}}
                        <div x-show="resetError" x-cloak
                            class="mb-3 flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                            <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-red-600">Reset failed</p>
                                <p class="text-[11px] text-red-500 mt-0.5 font-mono break-all" x-text="resetError">
                                </p>
                            </div>
                            <button @click="resetError = null" class="text-red-400 hover:text-red-600 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        {{-- Loading skeleton --}}
                        <div x-show="loading" class="space-y-2">
                            <template x-for="i in 5" :key="i">
                                <div
                                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-4 animate-pulse flex gap-4">
                                    <div class="w-8 flex flex-col gap-1 items-center">
                                        <div class="h-3 w-4 bg-lumot/30 rounded"></div>
                                        <div class="h-3 w-4 bg-lumot/20 rounded"></div>
                                        <div class="h-3 w-4 bg-lumot/30 rounded"></div>
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-lumot/30 rounded-lg w-2/4"></div>
                                        <div class="h-3 bg-lumot/20 rounded-lg w-3/4"></div>
                                        <div class="h-3 bg-lumot/20 rounded-lg w-1/2"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Empty --}}
                        <div x-show="!loading && rules.length === 0" x-cloak
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-12 text-center">
                            <div
                                class="w-14 h-14 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-gray/40" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-gray">No fee rules configured</p>
                            <p class="text-xs text-gray/60 mt-1">Add your first rule or reset to defaults.</p>
                            <button
                                @click="resetError = null; resetting = true; fetch('{{ route('bpls.fee-rules.reset-defaults') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } }).then(r => r.json()).then(d => { rules = d.rules; computePreview(); }).catch(e => { resetError = e.message; }).finally(() => resetting = false)"
                                class="mt-4 px-4 py-2 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-xl hover:bg-logo-teal/20 transition-colors">
                                Load Default Rules
                            </button>
                        </div>

                        {{-- Rules --}}
                        <div x-show="!loading && rules.length > 0" x-cloak class="space-y-2">
                            <template x-for="(rule, i) in rules" :key="rule.id">
                                <div class="bg-white rounded-2xl border shadow-sm transition-all duration-150 hover:shadow-md"
                                    :class="rule.enabled ? 'border-lumot/20 hover:border-logo-teal/30' :
                                        'border-lumot/10 opacity-60'">
                                    <div class="px-4 py-3.5 flex items-start gap-3">

                                        {{-- Order controls --}}
                                        <div class="flex flex-col gap-0.5 shrink-0 mt-0.5 items-center">
                                            <button @click="moveUp(i)" :disabled="i === 0"
                                                class="text-gray/30 hover:text-logo-teal disabled:opacity-20 disabled:cursor-not-allowed transition-colors text-xs leading-none">▲</button>
                                            <span class="text-[10px] text-gray/30 font-mono py-0.5"
                                                x-text="String(i+1).padStart(2,'0')"></span>
                                            <button @click="moveDown(i)" :disabled="i === rules.length - 1"
                                                class="text-gray/30 hover:text-logo-teal disabled:opacity-20 disabled:cursor-not-allowed transition-colors text-xs leading-none">▼</button>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <span class="text-sm font-extrabold text-green"
                                                    x-text="rule.name"></span>
                                                {{-- Formula badge --}}
                                                <span
                                                    class="text-[9px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full border"
                                                    :class="{
                                                        'bg-indigo-50 text-indigo-600 border-indigo-100': rule
                                                            .formula_type === 'graduated_rate',
                                                        'bg-amber-50 text-amber-600 border-amber-100': rule
                                                            .formula_type === 'scale_table',
                                                        'bg-bluebody text-gray border-lumot/30': rule
                                                            .formula_type === 'flat_amount',
                                                        'bg-logo-green/10 text-logo-green border-logo-green/20': rule
                                                            .formula_type === 'percentage',
                                                    }"
                                                    x-text="{
                                                        graduated_rate: 'Graduated Rate',
                                                        scale_table: 'Scale-Based',
                                                        flat_amount: 'Flat Amount',
                                                        percentage: 'Percentage',
                                                    }[rule.formula_type] || rule.formula_type">
                                                </span>
                                                <span x-show="!rule.enabled"
                                                    class="text-[9px] font-extrabold uppercase bg-lumot/30 text-gray/50 px-2 py-0.5 rounded-full border border-lumot/30">
                                                    Disabled
                                                </span>
                                            </div>

                                            {{-- Formula summary --}}
                                            <p class="text-[11px] text-gray/60 mb-1">
                                                <span x-show="rule.formula_type === 'graduated_rate'"
                                                    x-text="(rule.rate_table?.length ?? 0) + ' rate brackets on gross sales'"></span>
                                                <span x-show="rule.formula_type === 'scale_table'"
                                                    x-text="'Scale-based: ' + Object.values(rule.scale_table ?? {}).map(v => '₱'+Number(v).toLocaleString('en-PH')).join(' / ')"></span>
                                                <span x-show="rule.formula_type === 'flat_amount'"
                                                    x-text="'Fixed: ₱' + Number(rule.flat_amount ?? 0).toLocaleString('en-PH', {minimumFractionDigits:2})"></span>
                                                <span x-show="rule.formula_type === 'percentage'"
                                                    x-text="(rule.percentage ?? 0) + '% of gross sales'"></span>
                                            </p>

                                            <p x-show="rule.notes" class="text-[10px] text-gray/40 italic"
                                                x-text="rule.notes"></p>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            {{-- Toggle --}}
                                            <button @click="toggleRule(rule)"
                                                :class="rule.enabled ? 'bg-logo-teal' : 'bg-lumot/40'"
                                                class="w-9 h-5 rounded-full relative transition-colors shrink-0"
                                                :title="rule.enabled ? 'Disable rule' : 'Enable rule'">
                                                <span :class="rule.enabled ? 'left-4' : 'left-0.5'"
                                                    class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-all duration-200"></span>
                                            </button>
                                            {{-- Edit --}}
                                            <button @click="openEditModal(rule)"
                                                class="p-1.5 rounded-lg text-gray hover:text-logo-teal hover:bg-logo-teal/10 transition-colors"
                                                title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            {{-- Delete --}}
                                            <button @click="openDeleteModal(rule)"
                                                class="p-1.5 rounded-lg text-gray hover:text-red-500 hover:bg-red-50 transition-colors"
                                                title="Delete">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Expandable rate table --}}
                                    <div
                                        x-show="rule.formula_type === 'graduated_rate' && rule.rate_table?.length > 0">
                                        <details class="border-t border-lumot/10">
                                            <summary
                                                class="text-[10px] font-bold text-gray/40 uppercase px-4 py-2 cursor-pointer hover:text-gray hover:bg-bluebody/30 select-none transition-colors">
                                                View rate brackets (<span x-text="rule.rate_table?.length"></span>)
                                            </summary>
                                            <div class="px-4 pb-3 max-h-40 overflow-y-auto">
                                                <div class="grid grid-cols-2 gap-x-6">
                                                    <span class="text-[9px] font-bold text-gray/40 uppercase pb-1">Up
                                                        to (₱)</span>
                                                    <span
                                                        class="text-[9px] font-bold text-gray/40 uppercase pb-1 text-right">Rate</span>
                                                    <template x-for="(row, ri) in rule.rate_table"
                                                        :key="ri">
                                                        <>
                                                            <span class="text-[11px] text-gray py-0.5 font-mono"
                                                                x-text="row.max === null ? '∞ (any)' : Number(row.max).toLocaleString('en-PH')"></span>
                                                            <span
                                                                class="text-[11px] text-logo-teal font-bold py-0.5 text-right font-mono"
                                                                x-text="(Number(row.rate) * 100).toFixed(3) + '%'"></span>
                                                        </>
                                                    </template>
                                                </div>
                                            </div>
                                        </details>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- ── Live Preview Sidebar ── --}}
                    <div class="sticky top-5 space-y-4" x-show="!loading">
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-logo-teal to-logo-blue px-4 py-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-xs font-extrabold text-white uppercase tracking-wide">Live Preview</p>
                            </div>
                            <div class="p-4 space-y-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray/60 uppercase mb-1">Gross Sales
                                        (₱)</label>
                                    <input type="number" min="0" step="1000" x-model="preview.grossSales"
                                        @input="computePreview()"
                                        class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray/60 uppercase mb-1">Business
                                        Scale</label>
                                    <select x-model="preview.scale" @change="computePreview()"
                                        class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                        <template x-for="s in scales" :key="s.code">
                                            <option :value="s.code" x-text="s.label"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="border border-lumot/20 rounded-xl overflow-hidden">
                                    <div
                                        class="grid grid-cols-[1fr_auto] bg-lumot/20 px-3 py-1.5 border-b border-lumot/20">
                                        <span class="text-[9px] font-extrabold text-gray/60 uppercase">Fee</span>
                                        <span
                                            class="text-[9px] font-extrabold text-gray/60 uppercase text-right">Amount</span>
                                    </div>
                                    <template x-for="item in preview.breakdown" :key="item.name">
                                        <div class="grid grid-cols-[1fr_auto] px-3 py-2 border-b border-lumot/10"
                                            :class="!item.enabled ? 'opacity-40' : ''">
                                            <span class="text-[10px] text-gray leading-tight"
                                                x-text="item.name"></span>
                                            <span class="text-[10px] font-bold text-logo-teal text-right font-mono"
                                                x-text="'₱'+Number(item.amount).toLocaleString('en-PH',{minimumFractionDigits:2})"></span>
                                        </div>
                                    </template>
                                    <div
                                        class="grid grid-cols-[1fr_auto] px-3 py-2.5 bg-logo-teal/5 border-t-2 border-logo-teal/20">
                                        <span class="text-xs font-extrabold text-green">TOTAL</span>
                                        <span class="text-sm font-extrabold text-logo-teal font-mono"
                                            x-text="'₱'+Number(preview.total).toLocaleString('en-PH',{minimumFractionDigits:2})"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Warning notice --}}
                        <div class="bg-yellow/10 border border-yellow/30 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-yellow-700 uppercase mb-1">⚠ Note</p>
                            <p class="text-[10px] text-yellow-700 leading-relaxed">
                                Changes here take effect on the next assessment. Existing saved assessments are not
                                retroactively updated.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function feeRulesManager() {
                return {
                    rules: [],
                    loading: true,
                    resetting: false,
                    fetchError: null,
                    resetError: null,

                    scales: [{
                            code: 1,
                            label: 'Micro (Assets up to ₱3M)'
                        },
                        {
                            code: 2,
                            label: 'Small (₱3M – ₱15M)'
                        },
                        {
                            code: 3,
                            label: 'Medium (₱15M – ₱100M)'
                        },
                        {
                            code: 4,
                            label: 'Large (Above ₱100M)'
                        },
                        {
                            code: 5,
                            label: 'Enterprise'
                        },
                    ],

                    formulaTypes: [{
                            value: 'graduated_rate',
                            label: 'Graduated Rate',
                            sub: 'Brackets on gross sales'
                        },
                        {
                            value: 'scale_table',
                            label: 'Scale-Based',
                            sub: 'Fixed amount per scale'
                        },
                        {
                            value: 'flat_amount',
                            label: 'Flat Amount',
                            sub: 'Single fixed value'
                        },
                        {
                            value: 'percentage',
                            label: 'Percentage',
                            sub: '% of gross sales'
                        },
                    ],

                    preview: {
                        grossSales: 250000,
                        scale: 1,
                        breakdown: [],
                        total: 0,
                    },

                    // ── Modal ────────────────────────────────────────────────
                    modal: {
                        open: false,
                        editing: false,
                        saving: false,
                        error: null,
                        form: {
                            id: null,
                            name: '',
                            formula_type: 'flat_amount',
                            base_type: 'flat',
                            flat_amount: 0,
                            percentage: 0,
                            rate_table: [{
                                max: null,
                                rate: 0
                            }],
                            scale_table: {
                                1: 0,
                                2: 0,
                                3: 0,
                                4: 0,
                                5: 0
                            },
                            notes: '',
                            enabled: true,
                        },
                    },

                    deleteModal: {
                        open: false,
                        deleting: false,
                        rule: null,
                    },

                    // ── Lifecycle ────────────────────────────────────────────
                    async init() {
                        await this.fetchRules();
                        this.computePreview();
                    },

                    // ── API calls ────────────────────────────────────────────
                    async fetchRules() {
                        this.loading = true;
                        this.fetchError = null;
                        try {
                            const res = await fetch('{{ route('bpls.fee-rules.index') }}');
                            if (!res.ok) {
                                const err = await res.json().catch(() => ({}));
                                throw new Error(err.message || `Server error ${res.status}`);
                            }
                            const data = await res.json();
                            this.rules = data;
                            this.computePreview();
                        } catch (e) {
                            this.fetchError = e.message;
                            console.error('Fee rules fetch error:', e);
                        } finally {
                            this.loading = false;
                        }
                    },

                    async saveRule() {
                        this.modal.saving = true;
                        this.modal.error = null;
                        try {
                            const isEdit = !!this.modal.form.id;
                            const url = isEdit ?
                                `{{ url('bpls/fee-rules') }}/${this.modal.form.id}` :
                                '{{ route('bpls.fee-rules.store') }}';
                            const res = await fetch(url, {
                                method: isEdit ? 'PUT' : 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(this.modal.form),
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Save failed.');

                            await this.fetchRules();
                            this.closeModal();
                        } catch (e) {
                            this.modal.error = e.message;
                        } finally {
                            this.modal.saving = false;
                        }
                    },

                    async confirmDelete() {
                        this.deleteModal.deleting = true;
                        try {
                            const res = await fetch(`{{ url('bpls/fee-rules') }}/${this.deleteModal.rule.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                            });
                            if (!res.ok) throw new Error('Delete failed.');
                            await this.fetchRules();
                            this.deleteModal.open = false;
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.deleteModal.deleting = false;
                        }
                    },

                    async toggleRule(rule) {
                        try {
                            const res = await fetch(`{{ url('bpls/fee-rules') }}/${rule.id}/toggle`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error();
                            const idx = this.rules.findIndex(r => r.id === rule.id);
                            if (idx !== -1) this.rules[idx] = data.rule;
                            this.computePreview();
                        } catch (e) {
                            console.error('Toggle error:', e);
                        }
                    },

                    async moveUp(i) {
                        if (i === 0) return;
                        [this.rules[i - 1], this.rules[i]] = [this.rules[i], this.rules[i - 1]];
                        await this.saveOrder();
                    },

                    async moveDown(i) {
                        if (i === this.rules.length - 1) return;
                        [this.rules[i], this.rules[i + 1]] = [this.rules[i + 1], this.rules[i]];
                        await this.saveOrder();
                    },

                    async saveOrder() {
                        try {
                            await fetch('{{ route('bpls.fee-rules.reorder') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    ids: this.rules.map(r => r.id)
                                }),
                            });
                        } catch (e) {
                            console.error('Reorder error:', e);
                        }
                    },

                    async resetDefaults() {
                        if (!confirm('This will wipe all current rules and restore LGU defaults. Continue?')) return;
                        this.resetting = true;
                        this.resetError = null;
                        try {
                            const res = await fetch('{{ route('bpls.fee-rules.reset-defaults') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || `Server error ${res.status}`);
                            this.rules = data.rules;
                            this.fetchError = null;
                            this.computePreview();
                        } catch (e) {
                            this.resetError = e.message;
                            console.error('Reset error:', e);
                        } finally {
                            this.resetting = false;
                        }
                    },

                    // ── Modal helpers ────────────────────────────────────────
                    openCreateModal() {
                        this.modal.editing = false;
                        this.modal.error = null;
                        this.modal.form = {
                            id: null,
                            name: '',
                            formula_type: 'flat_amount',
                            base_type: 'flat',
                            flat_amount: 0,
                            percentage: 0,
                            rate_table: [{
                                max: null,
                                rate: 0
                            }],
                            scale_table: {
                                1: 0,
                                2: 0,
                                3: 0,
                                4: 0,
                                5: 0
                            },
                            notes: '',
                            enabled: true,
                        };
                        this.modal.open = true;
                    },

                    openEditModal(rule) {
                        this.modal.editing = true;
                        this.modal.error = null;
                        this.modal.form = {
                            id: rule.id,
                            name: rule.name,
                            formula_type: rule.formula_type,
                            base_type: rule.base_type,
                            flat_amount: rule.flat_amount ?? 0,
                            percentage: rule.percentage ?? 0,
                            rate_table: rule.rate_table ? JSON.parse(JSON.stringify(rule.rate_table)) : [{
                                max: null,
                                rate: 0
                            }],
                            scale_table: rule.scale_table ? JSON.parse(JSON.stringify(rule.scale_table)) : {
                                1: 0,
                                2: 0,
                                3: 0,
                                4: 0,
                                5: 0
                            },
                            notes: rule.notes ?? '',
                            enabled: rule.enabled,
                        };
                        this.modal.open = true;
                    },

                    closeModal() {
                        this.modal.open = false;
                    },

                    openDeleteModal(rule) {
                        this.deleteModal.rule = rule;
                        this.deleteModal.open = true;
                    },

                    onFormulaTypeChange() {
                        const ft = this.modal.form.formula_type;
                        this.modal.form.base_type =
                            ft === 'graduated_rate' || ft === 'percentage' ? 'gross_sales' :
                            ft === 'scale_table' ? 'scale' : 'flat';
                    },

                    addRateBracket() {
                        this.modal.form.rate_table.push({
                            max: null,
                            rate: 0
                        });
                    },

                    removeRateBracket(i) {
                        this.modal.form.rate_table.splice(i, 1);
                    },

                    // ── Client-side live preview ─────────────────────────────
                    computePreview() {
                        const gs = parseFloat(this.preview.grossSales) || 0;
                        const scale = parseInt(this.preview.scale) || 1;

                        this.preview.breakdown = this.rules.map(rule => ({
                            name: rule.name,
                            enabled: rule.enabled,
                            amount: rule.enabled ? this.computeRule(rule, gs, scale) : 0,
                        }));

                        this.preview.total = this.preview.breakdown
                            .filter(r => r.enabled)
                            .reduce((s, r) => s + r.amount, 0);
                    },

                    computeRule(rule, gs, scale) {
                        switch (rule.formula_type) {
                            case 'graduated_rate': {
                                const table = rule.rate_table ?? [];
                                const row = table.find(r => r.max === null || gs <= Number(r.max));
                                return gs * (row ? Number(row.rate) : 0);
                            }
                            case 'scale_table': {
                                const t = rule.scale_table ?? {};
                                return Number(t[scale] ?? t[String(scale)] ?? 0);
                            }
                            case 'flat_amount':
                                return Number(rule.flat_amount ?? 0);
                            case 'percentage':
                                return gs * (Number(rule.percentage ?? 0) / 100);
                            default:
                                return 0;
                        }
                    },
                }
            }
        </script>
    @endpush
</x-admin.app>
