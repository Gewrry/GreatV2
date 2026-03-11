{{-- resources/views/modules/bpls/onlineBPLS/application/partials/business-info.blade.php --}}
@php
    $b = $application->business;
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 hover:shadow-md transition-shadow">
    <h3 class="text-xs font-black text-green uppercase tracking-widest mb-4 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-logo-blue/20 to-logo-blue/5 flex items-center justify-center shadow-inner">
            <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
        </div>
        Business Details
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3 mb-4">
        @foreach ([
                'Business Name' => $b?->business_name,
                'Trade Name' => $b?->trade_name ?: '—',
                'TIN No.' => $b?->tin_no ?: '—',
                'Type' => $b?->type_of_business ?: '—',
                'Organization' => $b?->business_organization ?: '—',
                'Scale' => $b?->business_scale ?: '—',
                'Sector' => $b?->business_sector ?: '—',
                'Zone' => $b?->zone ?: '—',
                'Occupancy' => $b?->occupancy ?: '—',
                'Area (sqm)' => $b?->business_area_sqm ? number_format($b->business_area_sqm, 2) : '—',
                'Total Employees' => $b?->total_employees ?? '—',
                'LGU Employees' => $b?->employees_lgu ?? '—',
                'DTI/SEC/CDA No.' => $b?->dti_sec_cda_no ?: '—',
                'Reg. Date' => $b?->dti_sec_cda_date ? \Carbon\Carbon::parse($b->dti_sec_cda_date)->format('M d, Y') : '—',
                'Tax Incentive' => $b?->tax_incentive ? 'Yes' : 'No',
                'Business Mobile' => $b?->business_mobile ?: '—',
                'Business Email' => $b?->business_email ?: '—',
            ] as $lbl => $val)
            <div>
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">{{ $lbl }}</p>
                <p class="text-sm font-semibold text-green mt-0.5 break-all">{{ $val }}</p>
            </div>
        @endforeach
    </div>

    @if ($b?->amendment_from || $b?->amendment_to)
        <div class="pt-3 border-t border-lumot/20">
            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-2">Amendment</p>
            <div class="flex items-center gap-3 text-sm">
                <span class="font-semibold text-green">{{ $b->amendment_from ?: '—' }}</span>
                <svg class="w-4 h-4 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="font-semibold text-green">{{ $b->amendment_to ?: '—' }}</span>
            </div>
        </div>
    @endif

    <div class="mt-4 pt-4 border-t border-lumot/20">
        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">Business Address</p>
        <p class="text-sm text-green font-medium">
            {{ collect([$b?->street, $b?->barangay, $b?->municipality, $b?->province, $b?->region])->filter()->join(', ') ?: '—' }}
        </p>
    </div>
</div>
