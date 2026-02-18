<x-admin.app>
    @include('layouts.rpt.navigation')
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">
                <a href="{{ route('rpt.reports.index') }}" class="hover:text-logo-teal transition-colors">Reports Hub</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-600">Parcel List (Master Inventory)</span>
            </div>

            <!-- Header -->
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Parcel List Report</h1>
                    <p class="text-gray-500 mt-2 font-medium">Complete inventory of all property parcels by ARPN.</p>
                </div>
                <div class="flex gap-3">
                    <form action="{{ route('rpt.reports.parcel_list.export.pdf') }}" method="GET" id="export-form">
                        <input type="hidden" name="brgy_code" id="export-brgy">
                        <input type="hidden" name="classification" id="export-class">
                        <input type="hidden" name="status" id="export-status">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                            Export to PDF
                        </button>
                    </form>
                    <button class="bg-green-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-900/20 active:scale-95">
                        Export to Excel
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Barangay</label>
                        <select id="filter-barangay" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm font-bold focus:ring-logo-teal focus:border-logo-teal">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy->brgy_code }}">{{ $brgy->barangay_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Classification</label>
                        <select id="filter-classification" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm font-bold focus:ring-logo-teal focus:border-logo-teal">
                            <option value="">All Classifications</option>
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Agricultural">Agricultural</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Special">Special</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Status</label>
                        <select id="filter-status" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm font-bold focus:ring-logo-teal focus:border-logo-teal">
                            <option value="">All Status</option>
                            <option value="ACTIVE" selected>Active</option>
                            <option value="CANCELLED">Cancelled</option>
                            <option value="SUPERSEDED">Superseded</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button id="reset-filters" class="w-full bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition-colors text-xs uppercase tracking-widest">
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8">
                    <table id="parcel-report-table" class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">ARPN / TD No.</th>
                                <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Owner</th>
                                <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Location</th>
                                <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Classification</th>
                                <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Assessed Value</th>
                                <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(document).ready(function() {
        const table = $('#parcel-report-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('rpt.reports.parcel_list') }}",
                data: function(d) {
                    d.brgy_code = $('#filter-barangay').val();
                    d.classification = $('#filter-classification').val();
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                { 
                    data: 'arpn',
                    render: function(data, type, row) {
                        let arpn = data || 'N/A';
                        return `
                            <div class="flex flex-col">
                                <span class="font-black text-gray-900 text-sm">${arpn}</span>
                                <span class="text-[10px] font-bold text-logo-teal uppercase tracking-tight">${row.td_no}</span>
                            </div>
                        `;
                    }
                },
                { data: 'owner_names', name: 'owner_names', render: data => `<span class="text-xs font-bold text-gray-700">${data || 'N/A'}</span>` },
                { data: 'barangay_name', name: 'barangay_name', render: (data, type, row) => `<span class="text-xs font-medium text-gray-500">${data || row.bcode || 'N/A'}</span>` },
                { data: 'class', render: data => `<span class="px-3 py-1 bg-gray-50 text-[10px] font-bold text-gray-600 rounded-lg border border-gray-100 uppercase tracking-wide">${data || 'UNDEFINED'}</span>` },
                { 
                    data: 'total_assessed_value', 
                    className: 'text-right',
                    render: $.fn.dataTable.render.number(',', '.', 2, '₱')
                },
                { 
                    data: 'statt',
                    render: status => {
                        let colors = {
                            'ACTIVE': 'bg-green-100 text-green-700 border-green-200',
                            'CANCELLED': 'bg-red-100 text-red-700 border-red-200',
                            'SUPERSEDED': 'bg-amber-100 text-amber-700 border-amber-200',
                            'APPROVED': 'bg-blue-100 text-blue-700 border-blue-200'
                        };
                        let cls = colors[status] || 'bg-gray-100 text-gray-700 border-gray-200';
                        return `<span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border ${cls}">${status}</span>`;
                    }
                }
            ],
            dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
            pageLength: 25,
            ordering: false
        });

        $('#filter-barangay, #filter-classification, #filter-status').change(function() {
            table.draw();
            syncExportParams();
        });

        function syncExportParams() {
            $('#export-brgy').val($('#filter-barangay').val());
            $('#export-class').val($('#filter-classification').val());
            $('#export-status').val($('#filter-status').val());
        }

        syncExportParams();

        $('#reset-filters').click(function() {
            $('#filter-barangay').val('');
            $('#filter-classification').val('');
            $('#filter-status').val('ACTIVE');
            table.draw();
        });
    });
    </script>
    @endpush
</x-admin.app>
