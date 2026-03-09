<x-admin.app>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            #page-header {
                display: none !important;
            }

            @page {
                margin: 18mm;
                size: A4 landscape;
            }

            body {
                background: white !important;
                font-family: serif !important;
            }

            /* Strip all card styling — just text on white */
            .rounded-2xl,
            .rounded-xl {
                background: white !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin-bottom: 16pt !important;
            }

            /* KPI grid becomes a simple inline stat row */
            #kpi-grid {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 0 !important;
                border-top: 1.5pt solid black !important;
                border-bottom: 1.5pt solid black !important;
                padding: 6pt 0 !important;
                margin-bottom: 14pt !important;
            }

            #kpi-grid>div {
                background: white !important;
                border: none !important;
                border-right: 0.5pt solid #999 !important;
                padding: 4pt 12pt !important;
                flex: 1 1 auto !important;
            }

            #kpi-grid>div:last-child {
                border-right: none !important;
            }

            /* Suppress colored dots */
            #kpi-grid .w-2 {
                display: none !important;
            }

            #kpi-grid .text-3xl {
                font-size: 16pt !important;
                font-weight: bold !important;
                color: black !important;
                font-family: serif !important;
            }

            #kpi-grid .text-xs {
                font-size: 7pt !important;
                color: #333 !important;
                font-family: serif !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
            }

            /* Hero strip becomes a plain header band */
            .bg-gradient-to-br {
                background: white !important;
                border-top: 2pt solid black !important;
                border-bottom: 1pt solid black !important;
                padding: 8pt 0 !important;
                color: black !important;
            }

            .bg-gradient-to-br * {
                color: black !important;
            }

            #hs-rev {
                font-size: 22pt !important;
                font-weight: bold !important;
                font-family: serif !important;
            }

            /* Tables — government style */
            table {
                border-collapse: collapse !important;
                width: 100% !important;
                font-size: 8pt !important;
                font-family: serif !important;
            }

            th {
                border-top: 1.5pt solid black !important;
                border-bottom: 1pt solid black !important;
                padding: 4pt 6pt !important;
                text-align: left !important;
                font-weight: bold !important;
                color: black !important;
                background: none !important;
            }

            td {
                border-bottom: 0.5pt solid #ccc !important;
                padding: 3pt 6pt !important;
                color: black !important;
            }

            /* Status pills become plain text */
            span[class*="bg-"] {
                background: none !important;
                color: black !important;
                font-weight: bold !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }

            /* Chart section headings */
            h3 {
                font-size: 9pt !important;
                font-weight: bold !important;
                color: black !important;
                text-transform: uppercase !important;
                letter-spacing: 0.08em !important;
                border-bottom: 0.5pt solid #999 !important;
                padding-bottom: 3pt !important;
                margin-bottom: 6pt !important;
                font-family: serif !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .rounded-2xl {
                break-inside: avoid;
            }
        }
    </style>
    <div class="py-4 px-4 sm:px-6 lg:px-8 min-h-screen mx-auto">

        @include('layouts.bpls.navbar')

        {{-- ── PAGE HEADER ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 mb-6 bg-white p-2 rounded">
            <div>
                <p class="text-xs font-bold tracking-widest uppercase text-[#00A99D] mb-0.5">Business Permit & Licensing
                </p>
                <h1 class="text-2xl font-black text-[#10454F]">Dashboard</h1>
                <p class="text-md font-semibold text-gray-400 mt-0.5 " id="db-date"></p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <select id="year-sel"
                    class="text-sm font-semibold border-2 border-[#d8eaf9] rounded-xl px-3 py-2 text-[#10454F] bg-white focus:outline-none focus:border-[#00A99D] cursor-pointer">
                    @for ($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}
                        </option>
                    @endfor
                </select>
                <a href="{{ route('bpls.dashboard.print', ['year' => date('Y')]) }}" target="_blank"
                    class="flex items-center gap-1.5 text-sm font-bold px-4 py-2 rounded-xl border-2 border-[#d8eaf9] bg-white text-gray-500 hover:border-[#00A99D] hover:text-[#10454F] transition-colors no-print">
                    Print Report
                </a>
                <button id="dl-btn" onclick="downloadPDF()"
                    class="flex items-center gap-1.5 text-sm font-bold px-4 py-2 rounded-xl bg-[#10454F] text-[#BDE038] hover:bg-[#184C78] transition-colors no-print">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download PDF
                </button>
            </div>
        </div>

        <div id="bpls-dashboard">

            {{-- ── HERO REVENUE STRIP ── --}}
            <div
                class="rounded-2xl bg-gradient-to-br from-[#10454F] to-[#184C78] p-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black tracking-widest uppercase text-white/50">Revenue Collected</p>
                    <p class="text-3xl font-black text-[#BDE038] leading-none mt-1" id="hs-rev">₱0.00</p>
                    <p class="text-xs text-white/40 mt-1" id="hs-sub">Year {{ date('Y') }}</p>
                </div>
                <div class="flex gap-6 flex-wrap">
                    <div>
                        <p class="text-[10px] font-black tracking-widest uppercase text-white/40">Total Businesses</p>
                        <p class="text-2xl font-black text-white" id="hs-total">—</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black tracking-widest uppercase text-white/40">New This Year</p>
                        <p class="text-2xl font-black text-[#BDE038]" id="hs-new">—</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black tracking-widest uppercase text-white/40">Avg. Assessment</p>
                        <p class="text-2xl font-black text-[#7EC845]" id="hs-avg">—</p>
                    </div>
                </div>
            </div>

            {{-- ── KPI CARDS ── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6" id="kpi-grid">
                {{-- injected --}}
            </div>

            {{-- ── ROW 1: Monthly + Status ── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black text-[#10454F]">Registrations by Month</h3>
                        <span class="text-xs font-bold bg-[#d8eaf9] text-[#184C78] rounded-full px-2.5 py-0.5"
                            id="bdg-reg">{{ date('Y') }}</span>
                    </div>
                    <canvas id="c-monthly" height="200"></canvas>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black text-[#10454F]">Status Breakdown</h3>
                        <span class="text-xs font-bold bg-[#e6faf9] text-[#00A99D] rounded-full px-2.5 py-0.5"
                            id="bdg-status">—</span>
                    </div>
                    <canvas id="c-status" height="160"></canvas>
                    <div class="flex flex-wrap gap-x-4 gap-y-1.5 mt-3" id="st-legend"></div>
                </div>
            </div>

            {{-- ── REVENUE LINE ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-[#10454F]">Monthly Revenue (₱)</h3>
                    <span class="text-xs font-bold bg-[#e6faf9] text-[#00A99D] rounded-full px-2.5 py-0.5"
                        id="bdg-rev">{{ date('Y') }}</span>
                </div>
                <canvas id="c-revenue" height="80"></canvas>
            </div>

            {{-- ── ROW 2: Type + Scale + Mode ── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="text-sm font-black text-[#10454F] mb-4">Business Type</h3>
                    <canvas id="c-type" height="200"></canvas>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="text-sm font-black text-[#10454F] mb-4">Business Scale</h3>
                    <canvas id="c-scale" height="200"></canvas>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="text-sm font-black text-[#10454F] mb-4">Payment Mode</h3>
                    <canvas id="c-paymode" height="200"></canvas>
                </div>
            </div>

            {{-- ── ROW 3: Barangay + New vs Renewal ── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="text-sm font-black text-[#10454F] mb-4">Top Barangays</h3>
                    <canvas id="c-barangay" height="220"></canvas>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="text-sm font-black text-[#10454F] mb-4">New vs Renewal</h3>
                    <canvas id="c-renewal" height="220"></canvas>
                </div>
            </div>

            {{-- ── RECENT BUSINESSES ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-[#10454F]">Recent Applications</h3>
                    <a href="{{ route('bpls.business-list.index') }}"
                        class="text-xs font-bold px-3 py-1.5 rounded-xl border-2 border-[#d8eaf9] text-gray-500 hover:border-[#00A99D] hover:text-[#10454F] transition-colors">
                        View All →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[540px]">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Business</th>
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Owner</th>
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Type</th>
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Status</th>
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Applied</th>
                                <th class="text-right text-xs font-black uppercase tracking-wider text-gray-400 pb-2">
                                    Due</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-biz">
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-300 text-xs">Loading…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── RECENT PAYMENTS ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-[#10454F]">Recent Payments</h3>
                    <span class="text-xs font-bold bg-[#e6faf9] text-[#00A99D] rounded-full px-2.5 py-0.5"
                        id="bdg-pay">—</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[600px]">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    OR #</th>
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Business</th>
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Date</th>
                                <th
                                    class="text-right text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Amount</th>
                                <th
                                    class="text-right text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Collected</th>
                                <th
                                    class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2 pr-4">
                                    Method</th>
                                <th class="text-left text-xs font-black uppercase tracking-wider text-gray-400 pb-2">
                                    Received By</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-pay">
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-300 text-xs">Loading…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>{{-- /bpls-dashboard --}}
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        const C = {
            teal: '#00A99D',
            green: '#10454F',
            blue: '#184C78',
            lime: '#BDE038',
            lgreen: '#7EC845'
        };
        const PAL = [C.teal, C.blue, '#7c3aed', C.lgreen, '#d97706', C.green, '#0891b2', '#dc2626', '#0d9488', '#9333ea'];

        const ST_COL = {
            pending: '#eab308',
            for_payment: C.blue,
            for_renewal_payment: '#8b5cf6',
            approved: C.teal,
            completed: C.lgreen,
            rejected: '#ef4444',
            cancelled: '#9ca3af',
            retired: '#6b7280'
        };
        const ST_LBL = {
            pending: 'For Approval',
            for_payment: 'For Payment',
            for_renewal_payment: 'Renewal Pmt',
            approved: 'Approved',
            completed: 'Completed',
            rejected: 'Rejected',
            cancelled: 'Cancelled',
            retired: 'Retired'
        };
        const ST_PILL = {
            pending: 'bg-yellow-100 text-yellow-800',
            for_payment: 'bg-blue-100 text-blue-800',
            for_renewal_payment: 'bg-purple-100 text-purple-800',
            approved: 'bg-teal-100 text-teal-700',
            completed: 'bg-green-100 text-green-700',
            rejected: 'bg-red-100 text-red-700',
            cancelled: 'bg-gray-100 text-gray-600',
            retired: 'bg-gray-100 text-gray-500'
        };

        const CH = {};
        const kill = k => {
            if (CH[k]) {
                CH[k].destroy();
                delete CH[k];
            }
        };

        Chart.defaults.font.family = '"Nunito Sans", ui-sans-serif, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#9ca3af';

        const TT = {
            backgroundColor: '#10454F',
            titleColor: '#BDE038',
            bodyColor: '#fff',
            padding: 10,
            cornerRadius: 10,
            titleFont: {
                weight: 'bold'
            }
        };

        const baseOpts = (legend = false) => ({
            responsive: true,
            plugins: {
                legend: {
                    display: legend,
                    labels: {
                        boxWidth: 11,
                        padding: 10,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: TT
            },
            scales: {
                y: {
                    grid: {
                        color: '#f9fafb'
                    },
                    ticks: {
                        color: '#d1d5db'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#d1d5db'
                    }
                }
            }
        });

        async function load(year) {
            const r = await fetch(`{{ route('bpls.dashboard.data') }}?year=${year}`);
            const d = await r.json();
            render(d, year);
        }

        function render(d, year) {
            document.getElementById('db-date').textContent = 'As of ' + new Date().toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('hs-rev').textContent = '₱' + money(d.yearly_revenue ?? 0);
            document.getElementById('hs-sub').textContent = 'Year ' + year;
            document.getElementById('hs-total').textContent = fmt(d.total);
            document.getElementById('hs-new').textContent = fmt(d.new_this_year);
            document.getElementById('hs-avg').textContent = '₱' + money(d.avg_total_due ?? 0);
            renderKPIs(d);
            chartMonthly(d.monthly_registrations, year);
            chartStatus(d.status_counts);
            chartRevenue(d.monthly_revenue, year);
            chartType(d.type_counts);
            chartScale(d.scale_counts);
            chartPaymode(d.payment_mode_counts);
            chartBarangay(d.barangay_counts);
            chartRenewal(d.renewal_vs_new);
            tableBiz(d.recent_businesses);
            tablePay(d.recent_payments, d.total_collected);
        }

        function renderKPIs(d) {
            const sc = d.status_counts ?? {};
            const items = [{
                    l: 'Pending',
                    v: sc.pending ?? 0,
                    color: 'bg-yellow-50 border-yellow-200',
                    val: 'text-yellow-700',
                    dot: 'bg-yellow-400'
                },
                {
                    l: 'For Payment',
                    v: (sc.for_payment ?? 0) + (sc.for_renewal_payment ?? 0),
                    color: 'bg-blue-50 border-blue-200',
                    val: 'text-[#184C78]',
                    dot: 'bg-[#184C78]'
                },
                {
                    l: 'Approved',
                    v: sc.approved ?? 0,
                    color: 'bg-teal-50 border-teal-200',
                    val: 'text-[#00A99D]',
                    dot: 'bg-[#00A99D]'
                },
                {
                    l: 'Completed',
                    v: sc.completed ?? 0,
                    color: 'bg-green-50 border-green-200',
                    val: 'text-green-700',
                    dot: 'bg-[#7EC845]'
                },
                {
                    l: 'Rejected',
                    v: sc.rejected ?? 0,
                    color: 'bg-red-50 border-red-200',
                    val: 'text-red-700',
                    dot: 'bg-red-400'
                },
                {
                    l: 'Retired',
                    v: sc.retired ?? 0,
                    color: 'bg-gray-50 border-gray-200',
                    val: 'text-gray-500',
                    dot: 'bg-gray-400'
                },
                {
                    l: 'Renewals',
                    v: d.renewal_vs_new?.renewal ?? 0,
                    color: 'bg-purple-50 border-purple-200',
                    val: 'text-purple-700',
                    dot: 'bg-purple-400'
                },
                {
                    l: 'New This Year',
                    v: d.new_this_year ?? 0,
                    color: 'bg-teal-50 border-teal-200',
                    val: 'text-[#10454F]',
                    dot: 'bg-[#00A99D]'
                },
            ];
            document.getElementById('kpi-grid').innerHTML = items.map(k => `
        <div class="rounded-2xl border-2 ${k.color} p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-2 h-2 rounded-full ${k.dot}"></div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">${k.l}</span>
            </div>
            <div class="text-3xl font-black ${k.val}">${fmt(k.v)}</div>
        </div>
    `).join('');
        }

        function chartMonthly(data, year) {
            kill('mo');
            const MO = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const ctx = document.getElementById('c-monthly').getContext('2d');
            const g = ctx.createLinearGradient(0, 0, 0, 160);
            g.addColorStop(0, C.teal + '55');
            g.addColorStop(1, C.teal + '05');
            CH.mo = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: MO,
                    datasets: [{
                        data: MO.map((_, i) => data[i + 1] ?? 0),
                        backgroundColor: g,
                        borderColor: C.teal,
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: baseOpts(false)
            });
            document.getElementById('bdg-reg').textContent = year;
        }

        function chartStatus(counts) {
            kill('st');
            const keys = Object.keys(counts),
                vals = Object.values(counts),
                colors = keys.map(k => ST_COL[k] ?? '#9ca3af');
            const total = vals.reduce((a, b) => a + b, 0);
            CH.st = new Chart(document.getElementById('c-status'), {
                type: 'doughnut',
                data: {
                    labels: keys.map(k => ST_LBL[k] ?? k),
                    datasets: [{
                        data: vals,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            ...TT,
                            callbacks: {
                                label: ctx =>
                                    ` ${ST_LBL[ctx.label]??ctx.label}: ${ctx.parsed} (${Math.round(ctx.parsed/total*100)}%)`
                            }
                        }
                    }
                }
            });
            document.getElementById('bdg-status').textContent = total + ' total';
            document.getElementById('st-legend').innerHTML = keys.map((k, i) => `
        <div class="flex items-center gap-1.5 text-xs text-gray-500">
            <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:${colors[i]}"></div>
            ${ST_LBL[k]??k}: <span class="font-bold text-gray-700">${vals[i]}</span>
        </div>
    `).join('');
        }

        function chartRevenue(data, year) {
            kill('rv');
            const MO = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const ctx = document.getElementById('c-revenue').getContext('2d');
            const g = ctx.createLinearGradient(0, 0, 0, 100);
            g.addColorStop(0, C.teal + '30');
            g.addColorStop(1, C.teal + '00');
            CH.rv = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: MO,
                    datasets: [{
                        data: MO.map((_, i) => data[i + 1] ?? 0),
                        fill: true,
                        backgroundColor: g,
                        borderColor: C.teal,
                        borderWidth: 2.5,
                        tension: .4,
                        pointRadius: 4,
                        pointBackgroundColor: C.teal,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    ...baseOpts(false),
                    scales: {
                        y: {
                            ticks: {
                                callback: v => '₱' + money(v),
                                color: '#d1d5db'
                            },
                            grid: {
                                color: '#f9fafb'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#d1d5db'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            document.getElementById('bdg-rev').textContent = year;
        }

        function chartType(counts) {
            kill('bt');
            const s = Object.entries(counts).sort((a, b) => b[1] - a[1]).slice(0, 7);
            CH.bt = new Chart(document.getElementById('c-type'), {
                type: 'bar',
                data: {
                    labels: s.map(e => e[0] || '(none)'),
                    datasets: [{
                        data: s.map(e => e[1]),
                        backgroundColor: PAL.map(c => c + 'cc'),
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    ...baseOpts(false)
                }
            });
        }

        function chartScale(counts) {
            kill('bs');
            const e = Object.entries(counts).filter(x => x[0]);
            CH.bs = new Chart(document.getElementById('c-scale'), {
                type: 'pie',
                data: {
                    labels: e.map(x => x[0]),
                    datasets: [{
                        data: e.map(x => x[1]),
                        backgroundColor: PAL,
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 10
                                },
                                boxWidth: 10,
                                padding: 7
                            }
                        }
                    }
                }
            });
        }

        function chartPaymode(counts) {
            kill('pm');
            const map = {
                quarterly: 'Quarterly',
                semi_annual: 'Semi-Annual',
                annual: 'Annual'
            };
            const e = Object.entries(counts).filter(x => x[0]);
            CH.pm = new Chart(document.getElementById('c-paymode'), {
                type: 'doughnut',
                data: {
                    labels: e.map(x => map[x[0]] ?? x[0]),
                    datasets: [{
                        data: e.map(x => x[1]),
                        backgroundColor: [C.teal, C.blue, C.lime],
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    cutout: '55%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 10
                                },
                                boxWidth: 10,
                                padding: 7
                            }
                        }
                    }
                }
            });
        }

        function chartBarangay(counts) {
            kill('br');
            const s = Object.entries(counts).sort((a, b) => b[1] - a[1]).slice(0, 8);
            CH.br = new Chart(document.getElementById('c-barangay'), {
                type: 'bar',
                data: {
                    labels: s.map(e => e[0] || 'unknown'),
                    datasets: [{
                        data: s.map(e => e[1]),
                        backgroundColor: C.blue + 'bb',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    ...baseOpts(false)
                }
            });
        }

        function chartRenewal(data) {
            kill('rn');
            CH.rn = new Chart(document.getElementById('c-renewal'), {
                type: 'doughnut',
                data: {
                    labels: ['New Registration', 'Renewals'],
                    datasets: [{
                        data: [data.new ?? 0, data.renewal ?? 0],
                        backgroundColor: [C.teal, C.blue],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 11,
                                padding: 10
                            }
                        }
                    }
                }
            });
        }

        function tableBiz(rows) {
            const tb = document.getElementById('tbody-biz');
            if (!rows?.length) {
                tb.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-300 text-xs">No records</td></tr>';
                return;
            }
            tb.innerHTML = rows.map(r => `
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
            <td class="py-3 pr-4 font-bold text-[#10454F] text-sm">${esc(r.business_name)}</td>
            <td class="py-3 pr-4 text-xs text-gray-500">${esc(r.last_name)}, ${esc(r.first_name)}</td>
            <td class="py-3 pr-4 text-xs text-gray-400">${esc(r.type_of_business??'—')}</td>
            <td class="py-3 pr-4"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-bold ${ST_PILL[r.status]??'bg-gray-100 text-gray-500'}">${ST_LBL[r.status]??r.status}</span></td>
            <td class="py-3 pr-4 text-xs text-gray-400 whitespace-nowrap">${fmtDate(r.date_of_application)}</td>
            <td class="py-3 text-right text-sm font-bold text-[#10454F]">${r.total_due ? '₱'+money(r.total_due) : '—'}</td>
        </tr>
    `).join('');
        }

        function tablePay(rows, total) {
            const tb = document.getElementById('tbody-pay');
            document.getElementById('bdg-pay').textContent = '₱' + money(total ?? 0) + ' total';
            if (!rows?.length) {
                tb.innerHTML =
                    '<tr><td colspan="7" class="text-center py-8 text-gray-300 text-xs">No payments yet</td></tr>';
                return;
            }
            tb.innerHTML = rows.map(r => `
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
            <td class="py-3 pr-4 font-mono text-xs text-[#184C78] font-bold">${esc(r.or_number)}</td>
            <td class="py-3 pr-4 font-semibold text-sm text-[#10454F]">${esc(r.business_name??'—')}</td>
            <td class="py-3 pr-4 text-xs text-gray-400 whitespace-nowrap">${fmtDate(r.payment_date)}</td>
            <td class="py-3 pr-4 text-right text-sm">₱${money(r.amount_paid)}</td>
            <td class="py-3 pr-4 text-right text-sm font-bold text-[#10454F]">₱${money(r.total_collected)}</td>
            <td class="py-3 pr-4 text-xs capitalize text-gray-500">${esc(r.payment_method)}</td>
            <td class="py-3 text-xs text-gray-400">${esc(r.received_by??'—')}</td>
        </tr>
    `).join('');
        }

        async function downloadPDF() {
            const btn = document.getElementById('dl-btn');
            btn.textContent = 'Generating…';
            btn.disabled = true;
            const canvas = await html2canvas(document.getElementById('bpls-dashboard'), {
                scale: 1.5,
                useCORS: true,
                backgroundColor: '#f8fafc'
            });
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'landscape',
                unit: 'pt',
                format: 'a3'
            });
            const pw = pdf.internal.pageSize.getWidth(),
                ph = pdf.internal.pageSize.getHeight();
            const ih = (canvas.height * pw) / canvas.width;
            let y = 0;
            while (y < ih) {
                if (y > 0) pdf.addPage();
                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, -y, pw, ih);
                y += ph;
            }
            pdf.save(`BPLS_Dashboard_${document.getElementById('year-sel').value}.pdf`);
            btn.innerHTML =
                '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> Download PDF';
            btn.disabled = false;
        }

        const fmt = n => Number(n).toLocaleString('en-PH');
        const money = n => Number(n).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const fmtDate = d => d ? new Date(d).toLocaleDateString('en-PH', {
            year: '2-digit',
            month: 'short',
            day: 'numeric'
        }) : '—';
        const esc = s => s ? String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';

        document.getElementById('year-sel').addEventListener('change', e => load(e.target.value));
        load({{ date('Y') }});
    </script>
</x-admin.app>
