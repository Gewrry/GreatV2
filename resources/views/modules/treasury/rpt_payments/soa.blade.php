<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Property Ledger (SOA) - {{ $td->td_no }}</title>
    <style>
        @page { margin: 15mm; size: portrait; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; font-size: 10px; color: #1f2937; margin: 0; padding: 0; line-height: 1.3; }
        .container { max-width: 850px; margin: auto; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2.5px solid #0d9488; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; color: #0d9488; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 3px 0 0; color: #6b7280; font-size: 9px; }
        
        /* Property Details Grid */
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .detail-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; background: #f9fafb; }
        .detail-box h3 { margin: 0 0 6px; font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        .detail-item { margin-bottom: 3px; display: flex; justify-content: space-between; }
        .detail-label { font-weight: 600; color: #4b5563; }
        .detail-value { color: #111827; }

        /* Section Titles */
        .section-title { background: #0d9488; color: white; padding: 5px 10px; font-size: 10px; font-weight: bold; border-radius: 4px; margin: 20px 0 10px; text-transform: uppercase; display: flex; justify-content: space-between; }

        /* Table Style */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: 700; text-transform: uppercase; font-size: 8px; padding: 8px; border: 1px solid #e5e7eb; }
        td { padding: 8px; border: 1px solid #e5e7eb; vertical-align: middle; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .bg-gray-50 { background-color: #f9fafb; }
        
        /* Summary Section */
        .summary-wrapper { display: flex; justify-content: flex-end; margin-top: 10px; }
        .summary-table { width: 280px; }
        .summary-table td { border: none; padding: 3px 0; }
        .total-row td { border-top: 2px solid #0d9488; padding-top: 8px; font-size: 14px; color: #0d9488; font-weight: 800; }

        /* Signatures */
        .signature-section { margin-top: 35px; display: flex; justify-content: space-between; padding: 0 40px; }
        .sig-box { width: 200px; text-align: center; }
        .sig-line { border-bottom: 1.5px solid #1f2937; margin-bottom: 4px; padding-top: 30px; }
        .sig-name { font-size: 9px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .sig-title { font-size: 8px; color: #6b7280; margin: 0; }

        /* Footer */
        .footer { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 15px; font-size: 8px; color: #9ca3af; text-align: center; }
        
        /* Print Button */
        .no-print { position: fixed; bottom: 20px; right: 20px; background: #0d9488; color: white; border: none; padding: 10px 20px; border-radius: 50px; cursor: pointer; font-weight: 700; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 100; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Print Comprehensive Ledger</button>

    <div class="container">
        <div class="header">
            <h1>Comprehensive Property Ledger</h1>
            <p>Official Transaction History & Statement of Account · Generated: {{ now()->format('F d, Y h:i A') }}</p>
        </div>

        <div class="details-grid">
            <div class="detail-box">
                <h3>Property Registration</h3>
                <div class="detail-item">
                    <span class="detail-label">TD Number:</span>
                    <span class="detail-value font-bold">{{ $td->td_no }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">ARP Number:</span>
                    <span class="detail-value">{{ $td->property->arp_no ?? '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">PIN:</span>
                    <span class="detail-value font-bold">{{ $td->property->pin ?? $td->property->generateStructuredPin() }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Property Type:</span>
                    <span class="detail-value capitalize">{{ $td->property_kind }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value">Brgy. {{ $td->property->barangay?->name }}, {{ $td->property->municipality }}</span>
                </div>
            </div>
            <div class="detail-box">
                <h3>Owner Snapshot</h3>
                <div class="detail-item">
                    <span class="detail-label">Owner Name:</span>
                    <span class="detail-value font-bold">{{ $td->property->owner_name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Total Market Value:</span>
                    <span class="detail-value font-bold">₱ {{ number_format($td->total_market_value, 2) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Assessed Value:</span>
                    <span class="detail-value font-bold text-teal-700">₱ {{ number_format($td->total_assessed_value, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- ── SECTION 1: COMPLETED TRANSACTIONS ── --}}
        <div class="section-title">
            <span>Part I: Payment Audit Trail / Resources Transferred</span>
            <span>Total Accounted: ₱ {{ number_format($totalPaid, 2) }}</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="text-center">Date</th>
                    <th class="text-center">O.R. Number</th>
                    <th class="text-left">Details (Year/Quarter)</th>
                    <th class="text-left">Payment Mode</th>
                    <th class="text-right">Basic + SEF</th>
                    <th class="text-right">Penalty</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Total Paid</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td class="text-center">{{ $p->payment_date->format('m/d/Y') }}</td>
                    <td class="text-center font-bold">{{ $p->or_no }}</td>
                    <td class="text-left">{{ $p->billing?->tax_year }} - Q{{ $p->billing?->quarter }}</td>
                    <td class="text-left capitalize">{{ str_replace('_', ' ', $p->payment_mode) }}</td>
                    <td class="text-right">₱ {{ number_format($p->basic_tax + $p->sef_tax, 2) }}</td>
                    <td class="text-right text-red-500">₱ {{ number_format($p->penalty, 2) }}</td>
                    <td class="text-right text-green-600">₱ {{ number_format($p->discount, 2) }}</td>
                    <td class="text-right font-bold">₱ {{ number_format($p->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-gray-400 py-4">No historical transactions recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── SECTION 2: OUTSTANDING OBLIGATIONS ── --}}
        <div class="section-title">
            <span>Part II: Outstanding Balances & Obligations</span>
            <span>Unpaid Balance: ₱ {{ number_format($totalDue, 2) }}</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="text-center">Assessment Year</th>
                    <th class="text-center">Quarter</th>
                    <th class="text-right">Net Tax Due</th>
                    <th class="text-right">Penalty (2% Mo.)</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Amount Paid</th>
                    <th class="text-right">Current Balance</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billings as $b)
                <tr class="{{ $b->balance > 0 ? '' : 'bg-gray-50 text-gray-400' }}">
                    <td class="text-center font-bold">{{ $b->tax_year }}</td>
                    <td class="text-center">Q{{ $b->quarter }}</td>
                    <td class="text-right">₱ {{ number_format($b->total_tax_due, 2) }}</td>
                    <td class="text-right {{ $b->penalty_amount > 0 ? 'text-red-600 font-bold' : '' }}">₱ {{ number_format($b->penalty_amount, 2) }}</td>
                    <td class="text-right text-green-600">₱ {{ number_format($b->discount_amount, 2) }}</td>
                    <td class="text-right">₱ {{ number_format($b->amount_paid, 2) }}</td>
                    <td class="text-right font-bold {{ $b->balance > 0 ? 'text-teal-700' : '' }}">₱ {{ number_format($b->balance, 2) }}</td>
                    <td class="text-center">
                        <span style="font-size: 8px; padding: 2px 5px; border-radius: 3px; font-weight: bold; text-transform: uppercase; {{ $b->balance > 0 ? 'background: #fef3c7; color: #92400e;' : 'background: #d1fae5; color: #065f46;' }}">
                            {{ $b->balance > 0 ? ($b->amount_paid > 0 ? 'Partial' : 'Unpaid') : 'Paid' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td>Total Lifetime Paid:</td>
                    <td class="text-right font-bold">₱ {{ number_format($totalPaid, 2) }}</td>
                </tr>
                @if($totalDue > 0)
                <tr>
                    <td>Total Accrued Penalties:</td>
                    <td class="text-right text-red-600 font-bold">₱ {{ number_format($billings->sum('penalty_amount'), 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL OUTSTANDING DUE:</td>
                    <td class="text-right">₱ {{ number_format($totalDue, 2) }}</td>
                </tr>
                @else
                <tr class="total-row">
                    <td colspan="2" class="text-center">ACCOUNT FULLY SETTLED</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="signature-section">
            <div class="sig-box">
                <p style="text-align: left; margin-bottom: 5px;">Prepared by:</p>
                <div class="sig-line"></div>
                <p class="sig-name">{{ Auth::user()?->name ?? 'System Generated' }}</p>
                <p class="sig-title">{{ Auth::user() ? 'Revenue Collection Officer' : 'Online Portal' }}</p>
            </div>
            <div class="sig-box">
                <p style="text-align: left; margin-bottom: 5px;">Certified Correct:</p>
                <div class="sig-line"></div>
                <p class="sig-name">MUNICIPAL TREASURER</p>
                <p class="sig-title">Local Government Unit</p>
            </div>
        </div>

        <div class="footer">
            <p><strong>Comprehensive Disclosure:</strong> This document reflects all documented transfers of resources and obligations registered under this Tax Declaration.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} · Official Comprehensive Ledger</p>
        </div>
    </div>
</body>
</html>
