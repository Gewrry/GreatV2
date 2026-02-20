<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="px-8 py-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-10">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-2">FAAS Master List</h1>
                <p class="text-gray-500 font-medium flex items-center gap-2">
                    Real Property Field Appraisal and Assessment Management
                    <span class="w-1 h-1 bg-logo-teal rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-logo-teal">Live Database</span>
                </p>
            </div>
            <div class="flex items-center gap-4">
                <!-- New TD Button -->
                <a href="{{ route('rpt.td.create') }}"
                    class="group flex items-center gap-2 bg-gradient-to-br from-logo-teal to-indigo-600 text-white px-8 py-4 rounded-[1.5rem] text-sm font-black uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl shadow-logo-teal/20">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New TD</span>
                </a>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-gray-50/50 backdrop-blur-sm rounded-[2.5rem] p-8 mb-10 border border-gray-100 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
             <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Property Classification</label>
                <select id="filter-kind" class="w-full bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-logo-teal h-14 px-6 font-bold text-gray-700 appearance-none transition-all cursor-pointer hover:shadow-md">
                    <option value="">All Categories</option>
                    <option value="land">Real Estate (Land)</option>
                    <option value="building">Improvement (Building)</option>
                    <option value="machine">Equipment (Machine)</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Location / Barangay</label>
                <select id="filter-brgy" class="w-full bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-logo-teal h-14 px-6 font-bold text-gray-700 appearance-none transition-all cursor-pointer hover:shadow-md">
                    <option value="">Full Jurisdiction</option>
                    @foreach($barangays as $brgy)
                        <option value="{{ $brgy->bcode }}">{{ $brgy->brgy_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Assessment Status</label>
                <select id="filter-status" class="w-full bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-logo-teal h-14 px-6 font-bold text-gray-700 appearance-none transition-all cursor-pointer hover:shadow-md">
                    <option value="">All Statuses</option>
                    <option value="ACTIVE" class="text-green-600">Active</option>
                    <option value="DRAFT" class="text-gray-400">Draft</option>
                    <option value="FOR REVIEW" class="text-amber-500">For Review</option>
                    <option value="APPROVED" class="text-indigo-600">Approved</option>
                    <option value="CANCELLED" class="text-red-500">Cancelled</option>
                    <option value="inactive" class="text-gray-500">Archived / Superseded</option>
                </select>
            </div>
             <div class="relative">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Global Search</label>
                <div class="relative group">
                    <input type="text" id="custom-search" class="w-full bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-logo-teal h-14 pl-14 pr-6 font-bold text-gray-700 placeholder:text-gray-300 transition-all hover:shadow-md" placeholder="Search any fields...">
                    <svg class="w-6 h-6 text-gray-400 absolute left-5 top-4 group-focus-within:text-logo-teal transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="p-0 overflow-x-auto">
                <table id="faas-table" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900 border-none">
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Identification</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Location Details</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Ownership</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Assessment</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Year</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Exports</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Manage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <!-- Content -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Handle URL parameters for initial filtering
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('status')) {
                $('#filter-status').val(urlParams.get('status'));
            }

            var table = $('#faas-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                dom: 'tr<"px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4"ip>',
                ajax: {
                    url: "{{ route('rpt.faas_list') }}",
                    data: function(d) {
                        d.kind = $('#filter-kind').val();
                        d.brgy_code = $('#filter-brgy').val();
                        d.status = $('#filter-status').val();
                        d.search_value = $('#custom-search').val();
                    }
                },
                columns: [
                    {
                        data: 'arpn',
                        name: 'arpn',
                        className: 'px-8 py-6',
                        render: function(data, type, row) {
                            let historyBadge = '';
                            if (row.transferred_to) {
                                historyBadge = `<div class="mt-2 p-3 bg-amber-50 rounded-2xl border border-amber-200 shadow-sm">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></div>
                                        <p class="text-[9px] font-black text-amber-700 uppercase tracking-widest">Transferred To</p>
                                    </div>
                                    <p class="text-xs font-black text-gray-900 mb-0.5">${row.transferred_to.td_no}</p>
                                    <p class="text-[10px] font-bold text-gray-500 truncate">${row.transferred_to.owners}</p>
                                </div>`;
                            } else if (row.predecessor) {
                                historyBadge = `<div class="mt-2 p-3 bg-blue-50 rounded-2xl border border-blue-200 shadow-sm">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                        <p class="text-[9px] font-black text-blue-700 uppercase tracking-widest">Transferred From</p>
                                    </div>
                                    <p class="text-xs font-black text-gray-900">${row.predecessor.td_no}</p>
                                </div>`;
                            }

                            return `<div class="flex flex-col gap-1">
                                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">ARPN</span>
                                <span class="font-black text-gray-900 text-base leading-none">${data || 'UNASSIGNED'}</span>
                                <span class="text-[10px] font-bold text-logo-teal/80 bg-logo-teal/5 px-2 py-0.5 rounded-md inline-block w-fit mt-1">TD: ${row.td_no}</span>
                                ${historyBadge}
                            </div>`;
                        }
                    },
                    {
                        data: 'pin',
                        name: 'pin',
                        className: 'px-8 py-6',
                        render: function(data, type, row) {
                            return `<div class="flex flex-col gap-1">
                                <span class="font-black text-gray-900 text-sm tracking-tight">${data || 'N/A'}</span>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                    <span class="text-[10px] text-gray-500 font-black uppercase tracking-widest">${row.brgy}</span>
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: 'lot_no',
                        name: 'lot_no',
                        className: 'px-8 py-6',
                        render: function(data, type, row) {
                            const owners = row.owner_names;
                            return `<div class="flex flex-col gap-1">
                                <span class="font-black text-gray-900 text-xs uppercase tracking-tight truncate max-w-[250px]" title="${owners}">${owners}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Lot: ${data || 'N/A'}</span>
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: 'assessed_value',
                        name: 'assessed_value',
                        className: 'px-8 py-6 font-medium text-gray-900',
                        render: function(data, type, row) {
                            let statusClass = 'bg-gray-100 text-gray-400';
                            if (row.statt === 'ACTIVE' || row.statt === 'APPROVED') statusClass = 'bg-green-100 text-green-600';
                            if (row.statt === 'FOR REVIEW') statusClass = 'bg-amber-100 text-amber-600';
                            if (row.statt === 'DRAFT') statusClass = 'bg-blue-100 text-blue-600';
                            if (row.statt === 'CANCELLED') statusClass = 'bg-red-100 text-red-600';

                            return `<div class="flex flex-col gap-2">
                                <span class="font-black text-lg text-indigo-900 leading-none">${data}</span>
                                <span class="text-[9px] font-black uppercase tracking-[0.2em] px-3 py-1 rounded-full w-fit ${statusClass}">${row.statt}</span>
                            </div>`;
                        }
                    },
                    {
                        data: 'revised_year',
                        name: 'revised_year',
                        className: 'px-8 py-6 text-center font-black text-sm text-gray-600'
                    },
                    {
                        data: 'id',
                        name: 'prints',
                        className: 'px-8 py-6 text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const printUrl = `{{ url('rpt/td') }}/${data}/print`;
                            return `
                                <div class="flex items-center justify-center gap-2">
                                    <button title="Field Sheet" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-logo-teal hover:text-white transition-all shadow-sm">
                                        <span class="text-[10px] font-black">F</span>
                                    </button>
                                    <a href="${printUrl}" target="_blank" title="Tax Declaration" class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                        <span class="text-[10px] font-black">TD</span>
                                    </a>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'id', 
                        name: 'action', 
                        orderable: false, 
                        searchable: false,
                        className: 'px-8 py-6 text-center',
                        render: function(data, type, row) {
                            const editUrl = `{{ url('rpt/td') }}/${data}/edit`;
                            const transferUrl = `{{ url('rpt/td') }}/${data}/transfer`;
                            const deleteUrl = `{{ url('rpt/td') }}/${data}`;
                            
                            return `
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" type="button" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-logo-teal hover:text-logo-teal transition-all">
                                        OPTIONS
                                        <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         class="absolute right-0 mt-3 w-56 shadow-2xl bg-white rounded-[1.5rem] border border-gray-100 z-[100] py-2 overflow-hidden backdrop-blur-xl bg-white/95" 
                                         style="display: none;">
                                        
                                        <a href="${editUrl}" class="group flex items-center gap-4 px-6 py-3.5 text-xs font-black text-gray-500 hover:text-amber-600 transition-all uppercase tracking-widest">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            Modify Assessment
                                        </a>
                                        
                                        <a href="${transferUrl}" class="group flex items-center gap-4 px-6 py-3.5 text-xs font-black text-gray-500 hover:text-indigo-600 transition-all uppercase tracking-widest">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                            Transfer Owner
                                        </a>
                                        
                                        <div class="h-px bg-gray-50 mx-4 my-2"></div>

                                        <form action="${deleteUrl}" method="POST" onsubmit="return confirm('CRITICAL: This will permanently delete the Tax Declaration and all linked components. Proceed?');" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="group w-full flex items-center gap-4 px-6 py-3.5 text-xs font-black text-gray-400 hover:text-red-600 transition-all uppercase tracking-widest text-left">
                                                <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Delete Record
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    paginate: {
                        next: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>',
                        previous: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>'
                    },
                    processing: '<div class="flex items-center justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-4 border-logo-teal border-t-transparent"></div></div>'
                },
                drawCallback: function() {
                    $('.dataTables_paginate .paginate_button').addClass('flex items-center justify-center w-10 h-10 rounded-xl font-black text-xs transition-all mx-1 shadow-sm');
                    $('.dataTables_paginate .paginate_button.current').addClass('bg-logo-teal text-white shadow-logo-teal/20');
                    $('.dataTables_paginate .paginate_button:not(.current)').addClass('bg-white text-gray-400 hover:bg-gray-50');
                }
            });

            // Custom Filters
            $('#filter-kind, #filter-brgy, #filter-status').on('change', function() {
                table.draw();
            });

            $('#custom-search').on('keyup', function() {
                table.draw();
            });
        });
    </script>
    @endpush
</x-admin.app>