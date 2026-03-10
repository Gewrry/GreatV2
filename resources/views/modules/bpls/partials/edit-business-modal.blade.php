{{--
    resources/views/modules/bpls/partials/edit-business-modal.blade.php

    USAGE: @include('modules.bpls.partials.edit-business-modal')

    This partial requires the parent x-data component (businessList()) to have:
        - editModal state (defined in businessList() JS)
        - openEditModal(entry) method

    Routes needed:
        bpls.business-list.edit-data   GET  /bpls/business-list/{entry}/edit-data
        bpls.business-list.edit        POST /bpls/business-list/{entry}/edit
--}}

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- EDIT BUSINESS MODAL                                                       --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div x-show="editModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-green/40 backdrop-blur-sm" @click="closeEditModal()"></div>

    {{-- Panel --}}
    <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-3xl max-h-[94vh] flex flex-col"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2">

        {{-- ── Header ── --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-green">Edit Business</h3>
                    <p class="text-[11px] text-gray truncate max-w-[260px]" x-text="editModal.entry?.business_name"></p>
                </div>
            </div>

            {{-- Tab switcher --}}
            <div class="flex items-center gap-1 bg-lumot/20 rounded-xl p-1">
                <button @click="editModal.tab = 'edit'"
                    :class="editModal.tab === 'edit' ? 'bg-white shadow text-logo-blue' : 'text-gray hover:text-green'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </button>
                <button @click="editModal.tab = 'history'"
                    :class="editModal.tab === 'history' ? 'bg-white shadow text-logo-blue' : 'text-gray hover:text-green'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    History
                    <span x-show="editModal.amendments.length > 0"
                        class="bg-logo-blue text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full"
                        x-text="editModal.amendments.length"></span>
                </button>
            </div>

            <button @click="closeEditModal()"
                class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors ml-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- ── Loading state ── --}}
        <div x-show="editModal.loading" class="flex-1 flex items-center justify-center p-16">
            <div class="text-center">
                <svg class="w-8 h-8 animate-spin text-logo-teal mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <p class="text-xs text-gray/60 font-semibold">Loading business data…</p>
            </div>
        </div>

        {{-- ── Body ── --}}
        <div x-show="!editModal.loading" class="overflow-y-auto flex-1">

            {{-- ════════════════════════════════ EDIT TAB ════════════════════════════════ --}}
            <div x-show="editModal.tab === 'edit'" class="p-5 space-y-5">

                {{-- Rename notice banner — shown if business_name is being changed --}}
                <div x-show="editModal.form.business_name !== editModal.originalName && editModal.originalName"
                    class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-300 rounded-xl">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="min-w-0">
                        <p class="text-[11px] font-extrabold text-amber-800">Business Rename Detected</p>
                        <p class="text-[10px] text-amber-700 mt-0.5">
                            Renaming from <span class="font-bold" x-text="'\'' + editModal.originalName + '\''"></span>
                            to <span class="font-bold"
                                x-text="'\'' + (editModal.form.business_name || '…') + '\''"></span>.
                            The old name will be preserved in the amendment history for audit and reporting.
                        </p>
                    </div>
                </div>

                {{-- ── Section: Business Information ── --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1 border-b border-lumot/20">
                        <div class="w-5 h-5 rounded-lg bg-logo-teal/10 flex items-center justify-center">
                            <svg class="w-3 h-3 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase tracking-wider">Business
                            Information</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">
                                Business Name <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" x-model="editModal.form.business_name"
                                    :class="editModal.form.business_name !== editModal.originalName && editModal.originalName ?
                                        'border-amber-300 ring-2 ring-amber-100 bg-amber-50/30' :
                                        'border-lumot/30'"
                                    class="w-full text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-semibold transition-all">
                                <div x-show="editModal.form.business_name !== editModal.originalName && editModal.originalName"
                                    class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                            </div>
                            <p x-show="editModal.originalName && editModal.form.business_name !== editModal.originalName"
                                class="text-[10px] text-amber-600 mt-1 font-semibold flex items-center gap-1">
                                <span>Was:</span>
                                <span x-text="editModal.originalName" class="font-bold truncate"></span>
                            </p>
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Trade
                                Name</label>
                            <input type="text" x-model="editModal.form.trade_name"
                                :class="editModal.form.trade_name !== editModal.originalTradeName && editModal
                                    .originalTradeName !== undefined ?
                                    'border-amber-300 ring-1 ring-amber-100' :
                                    'border-lumot/30'"
                                class="w-full text-sm border rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">TIN
                                No.</label>
                            <input type="text" x-model="editModal.form.tin_no"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-mono">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Type of
                                Business</label>
                            <input type="text" x-model="editModal.form.type_of_business"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Business
                                Nature</label>
                            <input type="text" x-model="editModal.form.business_nature"
                                placeholder="e.g. Eatery, Trading, Manufacturing…"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Business
                                Scale</label>
                            <select x-model="editModal.form.business_scale"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                <option value="">-- Select Scale --</option>
                                <option value="Micro (Assets up to P3M)">Micro (Assets up to P3M)</option>
                                <option value="Small (P3M - P15M)">Small (P3M – P15M)</option>
                                <option value="Medium (P15M - P100M)">Medium (P15M – P100M)</option>
                                <option value="Large (Above P100M)">Large (Above P100M)</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Organization</label>
                            <input type="text" x-model="editModal.form.business_organization"
                                placeholder="e.g. Sole Proprietorship, Partnership…"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Zone</label>
                            <input type="text" x-model="editModal.form.zone"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Total
                                Employees</label>
                            <input type="number" x-model="editModal.form.total_employees" min="0"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Business
                                Mobile</label>
                            <input type="text" x-model="editModal.form.business_mobile"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Business
                                Email</label>
                            <input type="email" x-model="editModal.form.business_email"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                    </div>
                </div>

                {{-- ── Section: Business Address ── --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1 border-b border-lumot/20">
                        <div class="w-5 h-5 rounded-lg bg-logo-blue/10 flex items-center justify-center">
                            <svg class="w-3 h-3 text-logo-blue" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase tracking-wider">Business Address
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label
                                class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Barangay</label>
                            <input type="text" x-model="editModal.form.business_barangay"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Municipality</label>
                            <input type="text" x-model="editModal.form.business_municipality"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Street</label>
                            <input type="text" x-model="editModal.form.business_street"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                    </div>
                </div>

                {{-- ── Section: Owner Information ── --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1 border-b border-lumot/20">
                        <div class="w-5 h-5 rounded-lg bg-green/10 flex items-center justify-center">
                            <svg class="w-3 h-3 text-green" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase tracking-wider">Owner Information
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">
                                Last Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text" x-model="editModal.form.last_name"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">
                                First Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text" x-model="editModal.form.first_name"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Middle
                                Name</label>
                            <input type="text" x-model="editModal.form.middle_name"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Mobile
                                No.</label>
                            <input type="text" x-model="editModal.form.mobile_no"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">Email</label>
                            <input type="email" x-model="editModal.form.email"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                    </div>
                </div>

                {{-- ── Section: Amendment Reason ── --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-1 border-b border-lumot/20">
                        <div class="w-5 h-5 rounded-lg bg-orange-100 flex items-center justify-center">
                            <svg class="w-3 h-3 text-orange-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase tracking-wider">Amendment Reason
                            <span class="text-red-400">*</span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">
                            Reason for Amendment <span class="text-red-400">*</span>
                        </label>
                        <select x-model="editModal.form.reason"
                            @change="if(editModal.form.reason !== 'Other') editModal.form.reason_custom = ''"
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray mb-2">
                            <option value="">-- Select Reason --</option>
                            <option value="Correction of typographical error">Correction of typographical error
                            </option>
                            <option value="Business was renamed / rebranded">Business was renamed / rebranded</option>
                            <option value="Change of business address">Change of business address</option>
                            <option value="Change of ownership">Change of ownership</option>
                            <option value="Amendment per owner request">Amendment per owner request</option>
                            <option value="Amendment per BIR/DTI records">Amendment per BIR/DTI records</option>
                            <option value="Other">Other (specify below)</option>
                        </select>
                        <textarea x-show="editModal.form.reason === 'Other'" x-model="editModal.form.reason_custom" rows="2"
                            placeholder="Please specify reason…"
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 resize-none mt-1"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-gray/70 uppercase mb-1.5">
                            Additional Remarks <span class="font-normal text-gray/50">(optional)</span>
                        </label>
                        <textarea x-model="editModal.form.remarks" rows="2" placeholder="Any supporting notes for the record…"
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 resize-none"></textarea>
                    </div>
                </div>

                {{-- Changed fields preview --}}
                <div x-show="getChangedPreview().length > 0"
                    class="bg-amber-50 border border-amber-200 rounded-xl p-3 space-y-1.5">
                    <p
                        class="text-[10px] font-extrabold text-amber-700 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span
                            x-text="getChangedPreview().length + ' field' + (getChangedPreview().length !== 1 ? 's' : '') + ' changed'"></span>
                    </p>
                    <template x-for="diff in getChangedPreview()" :key="diff.field">
                        <div class="flex items-start gap-2">
                            <span class="text-[10px] font-bold text-amber-600 w-24 shrink-0"
                                x-text="diff.label"></span>
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="text-[10px] text-gray/50 line-through truncate max-w-[120px]"
                                    x-text="diff.old || '—'"></span>
                                <svg class="w-3 h-3 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                <span class="text-[10px] font-bold text-amber-800 truncate max-w-[120px]"
                                    x-text="diff.new || '—'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Error --}}
                <div x-show="editModal.error" x-cloak
                    class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-semibold text-red-500" x-text="editModal.error"></span>
                </div>

                {{-- Success --}}
                <div x-show="editModal.saved" x-cloak
                    class="flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl">
                    <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-xs font-semibold text-logo-green"
                        x-text="editModal.successMsg || 'Changes saved successfully!'"></span>
                </div>

            </div>

            {{-- ════════════════════════════════ HISTORY TAB ════════════════════════════════ --}}
            <div x-show="editModal.tab === 'history'" class="p-5">

                {{-- Empty state --}}
                <div x-show="editModal.amendments.length === 0" class="text-center py-12">
                    <div class="w-14 h-14 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray">No amendment history</p>
                    <p class="text-xs text-gray/50 mt-1">This business has not been edited yet.</p>
                </div>

                {{-- Timeline --}}
                <div x-show="editModal.amendments.length > 0" class="space-y-3">
                    <p class="text-[10px] font-extrabold text-gray/60 uppercase tracking-wider mb-4">Amendment Timeline
                    </p>

                    <template x-for="(amendment, idx) in editModal.amendments" :key="amendment.id">
                        <div class="flex gap-3">
                            {{-- Timeline dot --}}
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 border-2"
                                    :class="{
                                        'bg-amber-50 border-amber-300 text-amber-600': amendment
                                            .amendment_type === 'rename',
                                        'bg-blue-50 border-blue-300 text-blue-600': amendment
                                            .amendment_type === 'address_change',
                                        'bg-purple-50 border-purple-300 text-purple-600': amendment
                                            .amendment_type === 'owner_change',
                                        'bg-logo-teal/10 border-logo-teal/30 text-logo-teal': amendment
                                            .amendment_type === 'edit',
                                    }">
                                    {{-- Rename icon --}}
                                    <template x-if="amendment.amendment_type === 'rename'">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </template>
                                    {{-- Address icon --}}
                                    <template x-if="amendment.amendment_type === 'address_change'">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                    </template>
                                    {{-- Owner icon --}}
                                    <template x-if="amendment.amendment_type === 'owner_change'">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </template>
                                    {{-- Edit icon --}}
                                    <template x-if="amendment.amendment_type === 'edit'">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </template>
                                </div>
                                <div x-show="idx < editModal.amendments.length - 1"
                                    class="w-px flex-1 bg-lumot/30 mt-1 mb-1"></div>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0 pb-3">
                                <div class="flex items-start justify-between gap-2 mb-1.5">
                                    <div>
                                        <span class="text-[11px] font-extrabold"
                                            :class="{
                                                'text-amber-700': amendment.amendment_type === 'rename',
                                                'text-blue-600': amendment.amendment_type === 'address_change',
                                                'text-purple-600': amendment.amendment_type === 'owner_change',
                                                'text-logo-teal': amendment.amendment_type === 'edit',
                                            }"
                                            x-text="amendment.type_label"></span>
                                        <span class="text-[10px] text-gray/50 ml-2"
                                            x-text="amendment.amended_at"></span>
                                    </div>
                                    <span class="text-[9px] text-gray/40 shrink-0"
                                        x-text="'by ' + (amendment.amended_by_name || 'System')"></span>
                                </div>

                                {{-- Name change highlight --}}
                                <template x-if="amendment.amendment_type === 'rename' && amendment.old_business_name">
                                    <div
                                        class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-2 flex items-center gap-2">
                                        <span class="text-[11px] text-gray/50 line-through"
                                            x-text="amendment.old_business_name"></span>
                                        <svg class="w-3 h-3 text-amber-400 shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                        <span class="text-[11px] font-bold text-amber-800"
                                            x-text="amendment.new_business_name"></span>
                                    </div>
                                </template>

                                {{-- All changed fields diff --}}
                                <div class="space-y-0.5">
                                    <template x-for="(diff, di) in amendment.diff_summary" :key="di">
                                        <p class="text-[10px] text-gray/60" x-text="diff"></p>
                                    </template>
                                </div>

                                {{-- Reason + remarks --}}
                                <div x-show="amendment.reason" class="mt-1.5 flex items-start gap-1.5">
                                    <span
                                        class="text-[9px] font-bold text-gray/40 uppercase shrink-0 mt-0.5">Reason</span>
                                    <span class="text-[10px] text-gray/70 italic" x-text="amendment.reason"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- ── Footer ── --}}
        <div class="flex items-center justify-between gap-2 px-5 py-4 border-t border-lumot/20 shrink-0"
            x-show="editModal.tab === 'edit'">
            <button @click="closeEditModal()"
                class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                Cancel
            </button>
            <div class="flex items-center gap-3">
                <p x-show="getChangedPreview().length === 0 && !editModal.saved"
                    class="text-[11px] text-gray/40 italic">No changes yet</p>
                <button @click="saveEdit()"
                    :disabled="editModal.saving || !editModal.form.reason || (editModal.form.reason === 'Other' && !editModal
                        .form.reason_custom) || getChangedPreview().length === 0"
                    class="px-5 py-2 bg-logo-blue text-white text-sm font-bold rounded-xl hover:bg-green transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2 shadow-md shadow-logo-blue/20">
                    <svg x-show="editModal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <svg x-show="!editModal.saving" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span x-text="editModal.saving ? 'Saving…' : 'Save Changes'"></span>
                </button>
            </div>
        </div>

        <div class="flex justify-end gap-2 px-5 py-4 border-t border-lumot/20 shrink-0"
            x-show="editModal.tab === 'history'">
            <button @click="closeEditModal()"
                class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                Close
            </button>
        </div>

    </div>
</div>
