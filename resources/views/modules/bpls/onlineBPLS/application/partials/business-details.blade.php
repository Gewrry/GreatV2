{{-- resources/views/modules/bpls/onlineBPLS/application/partials/business-details.blade.php --}}
@php
    $b = $application->business;
    $o = $application->owner;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Business Info --}}
    <div class="bg-white rounded-3xl border border-lumot/20 shadow-sm p-6 hover:shadow-md transition-shadow">
        <h3 class="text-xs font-black text-green uppercase tracking-widest mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Business Information
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            @foreach ([
                ['Business ID', $b?->id ?? '—'],
                ['Business Name', $b?->business_name ?? '—'],
                ['Trade Name', $b?->trade_name ?? '—'],
                ['TIN No.', $b?->tin_no ?? '—'],
                ['Type', $b?->type_of_business ?? '—'],
                ['Organization', $b?->business_organization ?? '—'],
                ['Capital Investment', '₱' . number_format($b?->capital_investment ?? 0, 2)],
                ['Business Scale', $b?->business_scale ?? '—'],
            ] as [$label, $value])
                <div>
                    <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1">{{ $label }}</p>
                    <p class="text-sm font-black text-green uppercase tracking-tighter">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Owner Info --}}
    <div class="bg-white rounded-3xl border border-lumot/20 shadow-sm p-6 hover:shadow-md transition-shadow">
        <h3 class="text-xs font-black text-green uppercase tracking-widest mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Owner Information
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            @foreach ([
                ['Full Name', ($o?->first_name ?? '') . ' ' . ($o?->last_name ?? '')],
                ['Email (Login)', $application->client->email ?? '—'],
                ['Contact No.', $o?->contact_no ?? '—'],
                ['TIN No.', $o?->tin_no ?? '—'],
                ['Gender', ucfirst($o?->gender ?? '—')],
                ['Address', $o?->address ?? '—'],
            ] as [$label, $value])
                <div>
                    <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1">{{ $label }}</p>
                    <p class="text-sm font-black text-green uppercase tracking-tighter">{{ $value }}</p>
                </div>
            @endforeach
            <div class="sm:col-span-2 pt-3 border-t border-lumot/10 mt-2">
                <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2">Benefit Classifications</p>
                <div class="flex flex-wrap gap-2">
                    @php $hasBenefits = false; @endphp
                    @foreach (['is_senior' => 'Senior', 'is_pwd' => 'PWD', 'is_solo_parent' => 'Solo Parent', 'is_4ps' => '4Ps', 'is_bmbe' => 'BMBE', 'is_cooperative' => 'Cooperative'] as $key => $label)
                        @if ($o?->$key)
                            @php $hasBenefits = true; @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black bg-logo-teal/10 text-logo-teal border border-logo-teal/20 uppercase tracking-tighter">
                                {{ $label }}
                            </span>
                        @endif
                    @endforeach
                    @if (!$hasBenefits)
                        <span class="text-[10px] font-bold text-gray/30 italic">None specified</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
