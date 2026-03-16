{{-- resources/views/modules/bpls/onlineBPLS/application/partials/owner-info.blade.php --}}
@php
    $o = $application->owner;
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 hover:shadow-md transition-shadow">
    <h3 class="text-xs font-black text-green uppercase tracking-widest mb-4 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-logo-teal/20 to-logo-teal/5 flex items-center justify-center shadow-inner">
            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        Owner Information
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3">
        @foreach ([
                'Last Name' => $o?->last_name,
                'First Name' => $o?->first_name,
                'Middle Name' => $o?->middle_name ?: '—',
                'Citizenship' => $o?->citizenship ?: '—',
                'Civil Status' => $o?->civil_status ?: '—',
                'Gender' => $o?->gender ?: '—',
                'Birthdate' => $o?->birthdate ? \Carbon\Carbon::parse($o->birthdate)->format('M d, Y') : '—',
                'Mobile' => $o?->mobile_no ?: '—',
                'Email' => $o?->email ?: '—',
            ] as $lbl => $val)
            <div>
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">{{ $lbl }}</p>
                <p class="text-sm font-semibold text-green mt-0.5 break-all">{{ $val }}</p>
            </div>
        @endforeach
    </div>

    @php
        $classifications = collect([
            'PWD' => $o?->is_pwd,
            '4PS' => $o?->is_4ps,
            'Solo Parent' => $o?->is_solo_parent,
            'Senior Citizen' => $o?->is_senior,
            '10% Vaccinated' => $o?->discount_10,
            '5% 1st Dose' => $o?->discount_5,
        ])->filter()->keys();
    @endphp
    @if ($classifications->isNotEmpty())
        <div class="mt-3 pt-3 border-t border-lumot/20">
            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1.5">Classifications</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($classifications as $c)
                    <span class="text-[10px] font-bold px-2 py-1 bg-logo-teal/10 text-logo-teal rounded-full border border-logo-teal/20">{{ $c }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-3 pt-3 border-t border-lumot/20">
        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">Owner's Address</p>
        <p class="text-sm text-green font-medium">
            {{ collect([$o?->street, $o?->barangay, $o?->municipality, $o?->province, $o?->region])->filter()->join(', ') ?: '—' }}
        </p>
    </div>

    @if ($o?->emergency_contact_person)
        <div class="mt-3 pt-3 border-t border-lumot/20 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Emergency Contact</p>
                <p class="text-sm font-semibold text-green mt-0.5">{{ $o->emergency_contact_person }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Mobile</p>
                <p class="text-sm font-semibold text-green mt-0.5">{{ $o->emergency_mobile ?: '—' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Email</p>
                <p class="text-sm font-semibold text-green mt-0.5">{{ $o->emergency_email ?: '—' }}</p>
            </div>
        </div>
    @endif
</div>
