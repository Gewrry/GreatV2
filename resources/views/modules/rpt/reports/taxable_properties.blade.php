<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Taxable Properties Report</h1>
                    <p class="text-gray-500 mt-2 font-medium">List of all active properties subject to Real Property Tax.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Barangay</label>
                        <select name="brgy_code" id="brgy_code" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 text-sm font-semibold text-gray-700 bg-gray-50">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy->id }}">{{ $brgy->barangay_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Classification</label>
                        <select name="classification" id="classification" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 text-sm font-semibold text-gray-700 bg-gray-50">
                            <option value="">All Classifications</option>
                            <option value="Residential">Residential</option>
                            <option value="Agricultural">Agricultural</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Mineral">Mineral</option>
                            <option value="Timberland">Timberland</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="applyFilters" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                            Apply Filters
                        </button>
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="exportPDF" class="w-full py-2.5 bg-white text-gray-700 border border-gray-300 font-bold rounded-xl hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 group">
                            <svg class="w-5 h-5 text-red-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Export PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- Total Assessed Value Summary -->
            <div class="bg-gradient-to-r from-blue-900 to-blue-800 rounded-2xl shadow-lg p-6 mb-8 text-white flex justify-between items-center relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16 pointer-events-none"></div>
                <div class="relative z-10">
                    <h3 class="text-white/80 text-sm font-bold uppercase tracking-wider">Total Assessed Value (Taxable)</h3>
                    <p class="text-xs text-white/50 mt-1">Based on current active records</p>
                </div>
                <div class="relative z-10 text-right">
                    <p class="text-4xl font-black tracking-tighter drop-shadow-sm">₱{{ number_format($totalAssessedValue, 2) }}</p>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <table id="taxableTable" class="w-full" style="width:100%">
                        <thead>
                            <tr class="text-left border-b border-gray-100">
                                <th class="pb-4 pt-2 text-xs font-bold text-gray-400 uppercase tracking-wider pl-4">TD Number / PIN</th>
                                <th class="pb-4 pt-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Owner</th>
                                <th class="pb-4 pt-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Barangay</th>
                                <th class="pb-4 pt-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Class</th>
                                <th class="pb-4 pt-2 text-xs font-bold text-gray-400 uppercase tracking-wider text-right pr-4">Assessed Value</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-medium">
                            <!-- Populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#taxableTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rpt.reports.taxable_properties') }}",
                    data: function (d) {
                        d.brgy_code = $('#brgy_code').val();
                        d.classification = $('#classification').val();
                    }
                },
                columns: [
                    { 
                        data: 'td_no', 
                        name: 'td_no',
                        render: function(data, type, row) {
                            return `
                                <div class="flex flex-col pl-2">
                                    <span class="font-bold text-gray-900 hover:text-blue-600 transition-colors cursor-pointer">${data}</span>
                                    <span class="text-xs text-gray-400 font-mono tracking-tight">${row.pin || 'NO PIN'}</span>
                                </div>
                            `;
                        }
                    },
                    { 
                        data: 'owner_names', 
                        name: 'owner_names', 
                        className: 'font-bold text-gray-700' 
                    },
                    { 
                        data: 'barangay_name', 
                        name: 'barangay.barangay_name',
                        render: function(data) {
                            return `<span class="px-2.5 py-1 bg-gray-50 rounded-lg text-xs font-bold text-gray-500 uppercase tracking-wider border border-gray-100">${data}</span>`;
                        }
                    },
                    { 
                        data: 'class', 
                        name: 'class',
                        render: function(data) {
                            return `<span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold uppercase tracking-wider border border-blue-100">${data}</span>`;
                        }
                    },
                    { 
                        data: 'assessed_value', 
                        name: 'total_assessed_value', 
                        className: 'text-right font-black font-mono text-gray-800 pr-4 tracking-tight' 
                    },
                ],
                order: [[0, 'desc']],
                language: {
                    search: "",
                    searchPlaceholder: "Search Owner, TD No...",
                    processing: "<div class='flex items-center justify-center py-10'><div class='animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600'></div></div>"
                },
                dom: '<"flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4"<"flex items-center gap-2 text-sm text-gray-500"l><"flex-1 w-full md:w-auto flex justify-end"f>>rt<"flex flex-col md:flex-row items-center justify-between mt-6 gap-4"ip>',
                drawCallback: function() {
                    $('.dataTables_paginate .paginate_button')
                        .addClass('px-3 py-1 border border-gray-200 rounded-lg hover:bg-gray-50 mx-1 transition-all text-sm font-medium text-gray-600')
                        .removeClass('current');
                    $('.dataTables_paginate .paginate_button.current')
                        .addClass('bg-blue-600 text-white border-blue-600 hover:bg-blue-700 shadow-sm');
                    
                    $('.dataTables_wrapper input[type="search"]')
                        .addClass('w-full md:w-64 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-4 text-sm font-medium');
                }
            });

            $('#applyFilters').click(function() {
                table.draw();
            });

            $('#exportPDF').click(function() {
                let params = new URLSearchParams({
                    brgy_code: $('#brgy_code').val(),
                    classification: $('#classification').val()
                });
                window.location.href = "{{ route('rpt.reports.taxable_properties.export.pdf') }}?" + params.toString();
            });
        });
    </script>
    @endpush
</x-admin.app>
