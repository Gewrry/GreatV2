<x-admin.app>
    @include('layouts.rpt.navigation')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Fraunces:ital,wght@0,300;0,700;0,900;1,300;1,700&family=DM+Sans:wght@300;400;500;700&display=swap');

        :root {
            --teal: #0d9488;
            --teal-light: #14b8a6;
            --teal-dim: rgba(13, 148, 136, 0.08);
            --ink: #0f172a;
            --ink-mid: #334155;
            --ink-soft: #64748b;
            --surface: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
            --border-strong: #cbd5e1;
        }

        .faas-root {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            min-height: 100vh;
        }

        /* ── Header ─────────────────────────────────────────── */
        .faas-header {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 100%);
            position: relative;
            overflow: hidden;
            padding: 3rem 3rem 2.5rem;
        }

        .faas-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
        }

        .faas-header::after {
            content: '';
            position: absolute;
            top: -120px;
            right: -120px;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Extra bottom-left glow */
        .faas-header .glow-bl {
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(6, 78, 59, 0.5) 0%, transparent 70%);
            pointer-events: none;
        }

        .header-eyebrow {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: #6ee7b7;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-eyebrow::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 1px;
            background: #6ee7b7;
        }

        .header-title {
            font-family: 'Fraunces', serif;
            font-size: 3rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .header-title em {
            font-style: italic;
            color: #6ee7b7;
        }

        .header-subtitle {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .btn-new-td {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(8px);
            padding: 0.875rem 1.75rem;
            border-radius: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            position: relative;
            z-index: 1;
            white-space: nowrap;
        }

        .btn-new-td:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }

        /* ── Stats Bar ───────────────────────────────────────── */
        .stats-bar {
            background: #065f46;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0 3rem 1.5rem;
            display: flex;
            gap: 2.5rem;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .stat-value {
            font-family: 'Fraunces', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            line-height: 1;
        }

        .stat-label {
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.35);
        }

        .stat-divider {
            width: 1px;
            background: rgba(255, 255, 255, 0.1);
            align-self: stretch;
        }

        /* ── Filters ─────────────────────────────────────────── */
        .filters-panel {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 1.25rem 3rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 0 var(--border), 0 4px 16px rgba(0, 0, 0, 0.04);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            min-width: 160px;
        }

        .filter-label {
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--ink-soft);
        }

        .filter-select,
        .filter-input {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--ink);
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 0.625rem;
            height: 2.5rem;
            padding: 0 0.875rem;
            outline: none;
            transition: all 0.15s;
            cursor: pointer;
            width: 100%;
        }

        .filter-select:focus,
        .filter-input:focus {
            border-color: var(--teal);
            background: white;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        .filter-search-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .filter-search-wrap svg {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            color: var(--ink-soft);
            pointer-events: none;
        }

        .filter-input.search-input {
            padding-left: 2.25rem;
        }

        .filter-sep {
            width: 1px;
            height: 2rem;
            background: var(--border);
            flex-shrink: 0;
        }

        /* ── Table Container ─────────────────────────────────── */
        .table-wrap {
            padding: 2rem 3rem 4rem;
        }

        /* DataTables overrides */
        #faas-table {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0 0.375rem;
        }

        #faas-table thead tr {
            background: transparent;
        }

        #faas-table thead th {
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--ink-soft);
            font-weight: 500;
            padding: 0.5rem 1rem 0.75rem;
            border: none;
            background: transparent;
            white-space: nowrap;
        }

        #faas-table thead th:first-child {
            padding-left: 0;
        }

        #faas-table tbody tr {
            background: white;
            border-radius: 1rem;
            transition: all 0.18s ease;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0, 0, 0, 0.04);
        }

        #faas-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.09), 0 0 0 1.5px var(--teal);
        }

        #faas-table tbody td {
            padding: 1.25rem 1rem;
            border: none;
            vertical-align: top;
        }

        #faas-table tbody td:first-child {
            padding-left: 1.5rem;
            border-radius: 1rem 0 0 1rem;
        }

        #faas-table tbody td:last-child {
            padding-right: 1.5rem;
            border-radius: 0 1rem 1rem 0;
        }

        /* ── Cell Components ─────────────────────────────────── */
        .cell-arpn {
            font-family: 'DM Mono', monospace;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--ink);
            line-height: 1;
        }

        .cell-td-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 0.08em;
            color: var(--teal);
            background: var(--teal-dim);
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            margin-top: 0.35rem;
        }

        .cell-pin {
            font-family: 'DM Mono', monospace;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--ink-mid);
        }

        .cell-brgy {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--ink-soft);
            display: flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: 0.35rem;
        }

        .cell-brgy::before {
            content: '';
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #818cf8;
            flex-shrink: 0;
        }

        .cell-owner {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--ink);
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cell-lot {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            color: var(--ink-soft);
            margin-top: 0.3rem;
            letter-spacing: 0.05em;
        }

        .cell-value {
            font-family: 'Fraunces', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 500;
            padding: 0.3rem 0.65rem;
            border-radius: 2rem;
            margin-top: 0.5rem;
        }

        .status-pill .dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-pill.active {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-pill.active .dot {
            background: #16a34a;
            animation: pulse-dot 2s infinite;
        }

        .status-pill.approved {
            background: #e0e7ff;
            color: #4338ca;
        }

        .status-pill.approved .dot {
            background: #4338ca;
        }

        .status-pill.review {
            background: #fef9c3;
            color: #a16207;
        }

        .status-pill.review .dot {
            background: #eab308;
            animation: pulse-dot 1.5s infinite;
        }

        .status-pill.draft {
            background: #f1f5f9;
            color: #475569;
        }

        .status-pill.draft .dot {
            background: #94a3b8;
        }

        .status-pill.cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-pill.cancelled .dot {
            background: #dc2626;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(0.7);
            }
        }

        /* History Badges */
        .history-badge {
            margin-top: 0.625rem;
            padding: 0.625rem 0.75rem;
            border-radius: 0.75rem;
            border: 1.5px solid;
        }

        .history-badge.transfer {
            background: #fffbeb;
            border-color: #fcd34d;
        }

        .history-badge.subdiv {
            background: #f5f3ff;
            border-color: #c4b5fd;
        }

        .history-badge.from {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .history-badge-header {
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-bottom: 0.5rem;
        }

        .history-badge.transfer .history-badge-header {
            color: #92400e;
        }

        .history-badge.subdiv .history-badge-header {
            color: #6d28d9;
        }

        .history-badge.from .history-badge-header {
            color: #1d4ed8;
        }

        .history-badge-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .history-badge.transfer .history-badge-dot {
            background: #f59e0b;
        }

        .history-badge.subdiv .history-badge-dot {
            background: #8b5cf6;
            animation: pulse-dot 2s infinite;
        }

        .history-badge.from .history-badge-dot {
            background: #3b82f6;
        }

        .history-child-row {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
            padding: 0.4rem 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .history-child-row:first-child {
            border-top: none;
        }

        .history-child-td {
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--ink);
        }

        .history-child-owner {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.65rem;
            color: var(--ink-soft);
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Year cell */
        .cell-year {
            font-family: 'Fraunces', serif;
            font-size: 1.4rem;
            font-weight: 300;
            font-style: italic;
            color: var(--ink-mid);
            text-align: center;
        }

        /* Actions */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            border: 1.5px solid var(--border);
            background: white;
            color: var(--ink-soft);
            transition: all 0.15s;
            cursor: pointer;
            text-decoration: none;
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            font-weight: 500;
            letter-spacing: 0.05em;
        }

        .action-btn:hover {
            border-color: var(--teal);
            color: var(--teal);
            background: var(--teal-dim);
        }

        .action-btn.print:hover {
            border-color: #4f46e5;
            color: #4f46e5;
            background: #eef2ff;
        }

        /* Options Dropdown */
        .options-btn {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 500;
            color: var(--ink-soft);
            background: white;
            border: 1.5px solid var(--border);
            padding: 0.5rem 0.875rem;
            border-radius: 0.625rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            transition: all 0.15s;
        }

        .options-btn:hover {
            border-color: var(--teal);
            color: var(--ink);
        }

        .options-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            width: 14rem;
            background: white;
            border: 1.5px solid var(--border);
            border-radius: 1rem;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12), 0 4px 12px rgba(0, 0, 0, 0.06);
            z-index: 200;
            overflow: hidden;
            padding: 0.375rem;
        }

        .options-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.875rem;
            border-radius: 0.625rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--ink-mid);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.12s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .options-item:hover {
            background: var(--surface);
            color: var(--ink);
        }

        .options-item.danger:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        .options-sep {
            height: 1px;
            background: var(--border);
            margin: 0.25rem 0.5rem;
        }

        /* Pagination overrides */
        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            font-weight: 500;
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            border: 1.5px solid transparent;
            color: var(--ink-soft) !important;
            cursor: pointer;
            transition: all 0.12s;
            background: white !important;
            border-color: var(--border) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--teal-dim) !important;
            border-color: var(--teal) !important;
            color: var(--teal) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--teal) !important;
            border-color: var(--teal) !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.35;
            pointer-events: none;
        }

        .dataTables_wrapper .dataTables_info {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 0.08em;
            color: var(--ink-soft);
        }

        .dataTables_processing {
            background: white !important;
            border: 1.5px solid var(--border) !important;
            border-radius: 1rem !important;
            padding: 1.5rem 3rem !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
            font-family: 'DM Mono', monospace !important;
            font-size: 0.65rem !important;
            color: var(--ink-soft) !important;
            letter-spacing: 0.1em !important;
        }

        /* Row enter animation */
        @keyframes row-in {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #faas-table tbody tr {
            animation: row-in 0.2s ease both;
        }
    </style>

    <div class="faas-root">

        <!-- ── Header ───────────────────────────────────────────── -->
        <div class="faas-header">
            <div class="glow-bl"></div>
            <div class="relative z-10 flex items-end justify-between gap-8">
                <div>
                    <div class="header-eyebrow">Real Property Assessment System</div>
                    <h1 class="header-title">FAAS <em>Master</em> Registry</h1>
                    <p class="header-subtitle">Field Appraisal &amp; Assessment Sheet — Live Database</p>
                </div>
                <a href="{{ route('rpt.td.create') }}" class="btn-new-td">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    New Declaration
                </a>
            </div>
        </div>

        <!-- ── Stats Bar ─────────────────────────────────────────── -->
        <div class="stats-bar" id="stats-bar">
            <div class="stat-item">
                <span class="stat-value" id="stat-total">—</span>
                <span class="stat-label">Total Records</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-value" id="stat-active" style="color: #4ade80;">—</span>
                <span class="stat-label">Active</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-value" id="stat-cancelled" style="color: #f87171;">—</span>
                <span class="stat-label">Cancelled</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-value" id="stat-review" style="color: #fbbf24;">—</span>
                <span class="stat-label">For Review</span>
            </div>
        </div>

        <!-- ── Filters ───────────────────────────────────────────── -->
        <div class="filters-panel">
            <div class="filter-group">
                <span class="filter-label">Classification</span>
                <select id="filter-kind" class="filter-select">
                    <option value="">All Categories</option>
                    <option value="land">Land (Real Estate)</option>
                    <option value="building">Building (Improvement)</option>
                    <option value="machine">Machine (Equipment)</option>
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Barangay</span>
                <select id="filter-brgy" class="filter-select">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $brgy)
                        <option value="{{ $brgy->bcode }}">{{ $brgy->brgy_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Status</span>
                <select id="filter-status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="ACTIVE">Active</option>
                    <option value="DRAFT">Draft</option>
                    <option value="FOR REVIEW">For Review</option>
                    <option value="APPROVED">Approved</option>
                    <option value="CANCELLED">Cancelled</option>
                    <option value="inactive">Archived / Superseded</option>
                </select>
            </div>
            <div class="filter-sep"></div>
            <div class="filter-group filter-search-wrap" style="flex:1; min-width: 220px;">
                <span class="filter-label">Search</span>
                <div style="position:relative;">
                    <svg style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8" />
                        <path stroke-linecap="round" d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" id="custom-search" class="filter-input search-input"
                        placeholder="TD No, ARPN, PIN, Owner…" style="padding-left:2.25rem;">
                </div>
            </div>
        </div>

        <!-- ── Table ─────────────────────────────────────────────── -->
        <div class="table-wrap">
            <table id="faas-table" class="w-full">
                <thead>
                    <tr>
                        <th>Identification</th>
                        <th>Location</th>
                        <th>Ownership</th>
                        <th>Assessment</th>
                        <th style="text-align:center;">Year</th>
                        <th style="text-align:center;">Exports</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="flex items-center justify-between mt-6" id="dt-footer"></div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {

                // Pre-fill status filter from URL
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('status')) $('#filter-status').val(urlParams.get('status'));

                // ── Status helpers ──────────────────────────────────────
                function statusClass(s) {
                    if (!s) return 'draft';
                    s = s.toUpperCase();
                    if (s === 'ACTIVE') return 'active';
                    if (s === 'APPROVED') return 'approved';
                    if (s === 'FOR REVIEW') return 'review';
                    if (s === 'DRAFT') return 'draft';
                    if (s === 'CANCELLED') return 'cancelled';
                    return 'draft';
                }

                // ── DataTable ───────────────────────────────────────────
                var table = $('#faas-table').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 15,
                    dom: 'tr<"flex flex-col md:flex-row justify-between items-center gap-4 pt-6"ip>',
                    ajax: {
                        url: "{{ route('rpt.faas_list') }}",
                        data: function (d) {
                            // Send null (not empty string) for unset filters so
                            // the controller's filled() checks work correctly.
                            // This is the fix for the barangay filter not applying.
                            d.kind = $('#filter-kind').val() || null;
                            d.brgy_code = $('#filter-brgy').val() || null;
                            d.status = $('#filter-status').val() || null;
                            d.search_value = $('#custom-search').val() || null;
                        },
                        dataSrc: function (json) {
                            // Update stats bar from recordsTotal
                            if (json.stats) {
                                $('#stat-total').text(json.stats.total?.toLocaleString() ?? '—');
                                $('#stat-active').text(json.stats.active?.toLocaleString() ?? '—');
                                $('#stat-cancelled').text(json.stats.cancelled?.toLocaleString() ?? '—');
                                $('#stat-review').text(json.stats.review?.toLocaleString() ?? '—');
                            } else {
                                $('#stat-total').text(json.recordsTotal?.toLocaleString() ?? '—');
                            }
                            return json.data;
                        }
                    },
                    columns: [
                        // ── Identification ──────────────────────────────
                        {
                            data: 'arpn', name: 'arpn',
                            render: function (data, type, row) {
                                let badge = '';

                                if (row.transferred_to && row.transferred_to.length > 0) {
                                    const isSubdiv = row.transferred_to.length > 1;
                                    const cls = isSubdiv ? 'subdiv' : 'transfer';
                                    const label = isSubdiv
                                        ? `Subdivided → ${row.transferred_to.length} Parcels`
                                        : 'Transferred To';

                                    const rows = row.transferred_to.map(c => `
                                    <div class="history-child-row">
                                        <span class="history-child-td">${c.td_no}</span>
                                        <span class="history-child-owner" title="${c.owners}">${c.owners}</span>
                                    </div>`).join('');

                                    badge = `
                                    <div class="history-badge ${cls}">
                                        <div class="history-badge-header">
                                            <span class="history-badge-dot"></span>
                                            ${label}
                                        </div>
                                        ${rows}
                                    </div>`;

                                } else if (row.predecessor) {
                                    badge = `
                                    <div class="history-badge from">
                                        <div class="history-badge-header">
                                            <span class="history-badge-dot"></span>
                                            Originated From
                                        </div>
                                        <div class="history-child-row">
                                            <span class="history-child-td">${row.predecessor.td_no}</span>
                                        </div>
                                    </div>`;
                                }

                                return `
                                <div>
                                    <div class="cell-arpn">${data || 'UNASSIGNED'}</div>
                                    <div class="cell-td-badge">TD&nbsp;${row.td_no}</div>
                                    ${badge}
                                </div>`;
                            }
                        },
                        // ── Location ────────────────────────────────────
                        {
                            data: 'pin', name: 'pin',
                            render: function (data, type, row) {
                                return `
                                <div>
                                    <div class="cell-pin">${data || '—'}</div>
                                    <div class="cell-brgy">${row.brgy}</div>
                                </div>`;
                            }
                        },
                        // ── Ownership ───────────────────────────────────
                        {
                            data: 'lot_no', name: 'lot_no',
                            render: function (data, type, row) {
                                return `
                                <div>
                                    <div class="cell-owner" title="${row.owner_names}">${row.owner_names || '—'}</div>
                                    <div class="cell-lot">Lot&nbsp;${data || '—'}</div>
                                </div>`;
                            }
                        },
                        // ── Assessment ──────────────────────────────────
                        {
                            data: 'assessed_value', name: 'assessed_value',
                            render: function (data, type, row) {
                                const sc = statusClass(row.statt);
                                return `
                                <div>
                                    <div class="cell-value">${data}</div>
                                    <div>
                                        <span class="status-pill ${sc}">
                                            <span class="dot"></span>
                                            ${row.statt}
                                        </span>
                                    </div>
                                </div>`;
                            }
                        },
                        // ── Year ────────────────────────────────────────
                        {
                            data: 'revised_year', name: 'revised_year',
                            render: function (data) {
                                return `<div class="cell-year">${data || '—'}</div>`;
                            }
                        },
                        // ── Exports ─────────────────────────────────────
                        {
                            data: 'id', name: 'prints',
                            orderable: false, searchable: false,
                            render: function (data) {
                                const printUrl = `{{ url('rpt/td') }}/${data}/print`;
                                return `
                                <div style="display:flex;align-items:center;justify-content:center;gap:0.375rem;">
                                    <button class="action-btn" title="Field Sheet">F</button>
                                    <a href="${printUrl}" target="_blank" class="action-btn print" title="Tax Declaration">TD</a>
                                </div>`;
                            }
                        },
                        // ── Actions ─────────────────────────────────────
                        {
                            data: 'id', name: 'action',
                            orderable: false, searchable: false,
                            render: function (data, type, row) {
                                const editUrl = `{{ url('rpt/td') }}/${data}/edit`;
                                const transferUrl = `{{ url('rpt/td') }}/${data}/transfer`;
                                const deleteUrl = `{{ url('rpt/td') }}/${data}`;

                                return `
                                <div style="position:relative;display:inline-block;" x-data="{ open: false }">
                                    <button @click="open = !open" type="button" class="options-btn">
                                        Options
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"
                                             :style="open ? 'transform:rotate(180deg)' : ''">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="options-dropdown" style="display:none;">
                                        <a href="${editUrl}" class="options-item">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Modify Assessment
                                        </a>
                                        <a href="${transferUrl}" class="options-item">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            Transfer Owner
                                        </a>
                                        <div class="options-sep"></div>
                                        <form action="${deleteUrl}" method="POST"
                                              onsubmit="return confirm('CRITICAL: Permanently delete this Tax Declaration and all components?')"
                                              style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="options-item danger">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Delete Record
                                            </button>
                                        </form>
                                    </div>
                                </div>`;
                            }
                        }
                    ],
                    language: {
                        paginate: {
                            next: '›',
                            previous: '‹',
                        },
                        processing: '<span style="font-family:\'DM Mono\',monospace;font-size:0.65rem;letter-spacing:0.1em;color:#64748b;">LOADING RECORDS…</span>',
                        emptyTable: '<div style="padding:3rem;font-family:\'DM Mono\',monospace;font-size:0.65rem;letter-spacing:0.1em;color:#94a3b8;text-align:center;">NO RECORDS FOUND</div>',
                        info: 'Showing _START_–_END_ of _TOTAL_',
                        infoFiltered: '(filtered from _MAX_)',
                    },
                    drawCallback: function () {
                        // Stagger row animations
                        $('#faas-table tbody tr').each(function (i) {
                            $(this).css('animation-delay', (i * 0.03) + 's');
                        });
                    }
                });

                // ── Filters ─────────────────────────────────────────────
                // Send empty string as null so controller's filled() check works correctly
                $('#filter-kind, #filter-brgy, #filter-status').on('change', function () { table.draw(); });
                $('#custom-search').on('input', function () { table.draw(); });
            });
        </script>
    @endpush
</x-admin.app>