<x-admin.app>
    @include('layouts.rpt.navigation')
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">
                <a href="{{ route('rpt.reports.index') }}" class="hover:text-logo-teal transition-colors">Reports Hub</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-600">RPU List (Component Inventory)</span>
            </div>

            <!-- Header -->
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">RPU List Report</h1>
                    <p class="text-gray-500 mt-2 font-medium">Detailed listing of all individual property components.</p>
                </div>
                <div class="flex gap-3">
                    <form action="{{ route('rpt.reports.rpu_list.export.pdf') }}" method="GET" id="export-form">
                        <input type="hidden" name="rpu_type" id="export-type" value="LAND">
                        <input type="hidden" name="brgy_code" id="export-brgy">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                            Export PDF
                        </button>
                    </form>
                    <button class="bg-green-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-900/20 active:scale-95">
                        Export Excel
                    </button>
                </div>
            </div>

            <!-- Type Selector & Filters -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8">
                <div class="flex gap-4 mb-8 p-1 bg-gray-100 rounded-2xl w-fit">
                    <button data-type="LAND" class="rpu-tab-btn px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all bg-white text-logo-teal shadow-md">Land</button>
                    <button data-type="BUILDING" class="rpu-tab-btn px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-gray-500 hover:text-gray-800">Building</button>
                    <button data-type="MACHINE" class="rpu-tab-btn px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-gray-500 hover:text-gray-800">Machinery</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Barangay</label>
                        <select id="filter-barangay" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm font-bold focus:ring-logo-teal focus:border-logo-teal">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy->brgy_code }}">{{ $brgy->barangay_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8">
                    <table id="rpu-report-table" class="w-full text-left">
                        <thead id="table-head">
                            <!-- Dynamic Head -->
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(document).ready(function() {
        let currentType = 'LAND';
        
        const landColumns = [
            { data: 'arpn', title: 'ARPN', render: data => `<span class="text-xs font-black text-gray-900">${data || 'N/A'}</span>` },
            { data: 'owner_names', title: 'Owner', render: data => `<span class="text-xs font-bold text-gray-700">${data || 'N/A'}</span>` },
            { data: 'lot_no', title: 'Lot No.', render: data => `<span class="text-xs font-medium text-gray-600">${data || 'N/A'}</span>` },
            { data: 'survey_no', title: 'Survey No.', render: data => `<span class="text-xs font-medium text-gray-600">${data || 'N/A'}</span>` },
            { data: 'area', title: 'Area (sqm)', className: 'text-right', render: data => `<span class="text-xs font-bold text-gray-900">${parseFloat(data).toLocaleString()}</span>` },
            { data: 'assessed_value', title: 'Assessed Value', className: 'text-right', render: $.fn.dataTable.render.number(',', '.', 2, '₱') }
        ];

        const buildingColumns = [
            { data: 'arpn', title: 'ARPN', render: data => `<span class="text-xs font-black text-gray-900">${data || 'N/A'}</span>` },
            { data: 'owner_names', title: 'Owner', render: data => `<span class="text-xs font-bold text-gray-700">${data || 'N/A'}</span>` },
            { data: 'building_type', title: 'Type', render: data => `<span class="text-xs font-bold text-blue-600 uppercase tracking-tight">${data}</span>` },
            { data: 'structure_type', title: 'Structure', render: data => `<span class="text-xs font-medium text-gray-500">${data}</span>` },
            { data: 'floor_area', title: 'Floor Area', className: 'text-right', render: data => `<span class="text-xs font-bold text-gray-900">${parseFloat(data).toLocaleString()}</span>` },
            { data: 'assessed_value', title: 'Assessed Value', className: 'text-right', render: $.fn.dataTable.render.number(',', '.', 2, '₱') }
        ];

        const machineColumns = [
            { data: 'arpn', title: 'ARPN', render: data => `<span class="text-xs font-black text-gray-900">${data || 'N/A'}</span>` },
            { data: 'owner_names', title: 'Owner', render: data => `<span class="text-xs font-bold text-gray-700">${data || 'N/A'}</span>` },
            { data: 'machine_name', title: 'Machine Name', render: data => `<span class="text-xs font-bold text-purple-600 uppercase tracking-tight">${data}</span>` },
            { data: 'brand_model', title: 'Brand/Model', render: data => `<span class="text-xs font-medium text-gray-500">${data || 'N/A'}</span>` },
            { data: 'serial_no', title: 'Serial No.', render: data => `<span class="text-xs font-medium text-gray-500 font-mono">${data || 'N/A'}</span>` },
            { data: 'assessed_value', title: 'Assessed Value', className: 'text-right', render: $.fn.dataTable.render.number(',', '.', 2, '₱') }
        ];

        let table;

        function initTable(type) {
            if (table) table.destroy();
            $('#rpu-report-table').empty();

            let columns = landColumns;
            if (type === 'BUILDING') columns = buildingColumns;
            if (type === 'MACHINE') columns = machineColumns;

            table = $('#rpu-report-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rpt.reports.rpu_list') }}",
                    data: function(d) {
                        d.rpu_type = type;
                        d.brgy_code = $('#filter-barangay').val();
                    }
                },
                columns: columns,
                dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                pageLength: 25,
                ordering: false
            });
        }

        initTable('LAND');

        function syncExportParams() {
            $('#export-type').val(currentType);
            $('#export-brgy').val($('#filter-barangay').val());
        }

        $('.rpu-tab-btn').click(function() {
            $('.rpu-tab-btn').removeClass('bg-white text-logo-teal shadow-md').addClass('text-gray-500 hover:text-gray-800');
            $(this).removeClass('text-gray-500 hover:text-gray-800').addClass('bg-white text-logo-teal shadow-md');
            
            currentType = $(this).data('type');
            initTable(currentType);
            syncExportParams();
        });

        $('#filter-barangay').change(function() {
            table.draw();
            syncExportParams();
        });

        syncExportParams();
    });
    </script>
    @endpush
</x-admin.app>
