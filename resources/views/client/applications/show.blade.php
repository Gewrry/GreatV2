{{-- resources/views/client/applications/show.blade.php --}}
@extends('client.layouts.app')

@section('title', 'Application ' . $application->application_number)

@section('content')
    @php
        $status    = $application->workflow_status;
        $stages    = ['submitted' => 'Verification', 'verified' => 'Assessment', 'assessed' => 'Payment', 'paid' => 'For Approval', 'approved' => 'Approved'];
        $stageKeys = array_keys($stages);
        $curIdx    = array_search($status, $stageKeys);
        $rejected  = $status === 'rejected';
        $returned  = $status === 'returned';
    @endphp
    <div class="max-w-5xl mx-auto px-4">

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Back + Header (Glassmorphism inspired) --}}
        <div class="mb-8 p-6 bg-gradient-to-r from-green/90 to-logo-teal/90 backdrop-blur-xl rounded-[2.5rem] shadow-2xl shadow-logo-teal/20 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-logo-green/20 rounded-full blur-3xl"></div>

            <a href="{{ route('client.applications.index') }}" class="text-[10px] font-black uppercase tracking-widest text-white/70 hover:text-white transition-all mb-4 inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 rounded-xl border border-white/10">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Dashboard
            </a>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-3xl font-black tracking-tightest leading-none">{{ $application->application_number }}</h1>
                        <div class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded-full text-[10px] font-black uppercase tracking-widest">
                            {{ $application->status_label }}
                        </div>
                    </div>
                    <p class="text-white/80 text-xs font-bold mt-2 uppercase tracking-wide">
                        {{ $application->business->business_name ?? '—' }} · {{ ucfirst($application->application_type) }} 2026
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @if(in_array($application->workflow_status, ['draft','returned']))
                        <a href="{{ route('client.documents.index', $application->id) }}"
                            class="px-6 py-3 bg-white text-logo-teal text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-bluebody/10 hover:text-white transition-all shadow-xl shadow-black/10 border border-transparent hover:border-white/50">
                            📄 Upload Files
                        </a>
                    @elseif($application->workflow_status === 'assessed')
                        <a href="{{ route('client.payment.show', $application->id) }}"
                            class="px-8 py-3 bg-orange-500 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-orange-600 transition-all shadow-xl shadow-orange-500/30 animate-pulse border-2 border-white/20">
                            💳 Pay Now
                        </a>
                    @elseif($application->workflow_status === 'approved' || ($application->workflow_status === 'paid' && $application->isPaymentSatisfiedForApproval()))
                        <a href="{{ route('client.applications.permit.download', $application->id) }}"
                            class="px-6 py-3 bg-white text-green text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-green hover:text-white transition-all shadow-xl shadow-black/10">
                            ⬇️ Download Permit
                        </a>
                        @if($application->workflow_status === 'approved')
                            <a href="{{ route('client.applications.retire.form', $application->id) }}" class="p-3 bg-red-500/20 text-white/70 hover:text-white hover:bg-red-500 transition-all rounded-2xl border border-white/10 tooltip" title="Retire Business">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            </a>
                        @endif
                    @endif

                    <form action="{{ route('client.applications.destroy', $application->id) }}" method="POST" onsubmit="return confirm('Delete this application?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-3 bg-red-500/20 text-white/50 hover:text-white hover:bg-red-500 transition-all rounded-2xl border border-white/10 tooltip" title="Delete Application">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Horizontal Stepper Integrated in Header --}}
            <div class="mt-8 pt-6 border-t border-white/10">
                <div class="flex items-center justify-between max-w-3xl mx-auto px-4">
                    @foreach ($stages as $key => $label)
                        @php
                            $idx    = array_search($key, $stageKeys);
                            $done   = $curIdx !== false && $idx < $curIdx && !$rejected && !$returned;
                            $active = $application->workflow_status === $key && !$rejected && !$returned;
                        @endphp
                        <div class="flex flex-col items-center relative group">
                            <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-[10px] font-black transition-all duration-500 z-10
                                {{ $done   ? 'bg-white text-green shadow-lg shadow-black/10' : '' }}
                                {{ $active ? 'bg-white text-logo-teal shadow-2xl shadow-black/20 scale-125 border-2 border-logo-teal/20' : '' }}
                                {{ !$done && !$active ? 'bg-white/10 text-white/30 border border-white/10' : '' }}">
                                @if ($done)
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ $idx + 1 }}
                                @endif
                            </div>
                            <span class="absolute -bottom-6 text-[9px] font-black uppercase tracking-tighter transition-all duration-300 whitespace-nowrap
                                {{ $done   ? 'text-white/60' : '' }}
                                {{ $active ? 'text-white translate-y-1' : '' }}
                                {{ !$done && !$active ? 'text-white/20' : '' }}">
                                {{ $label }}
                            </span>
                            
                            @if (!$loop->last)
                                <div class="absolute left-1/2 top-4.5 w-full h-[2px] -z-0 ml-4.5
                                    {{ $done ? 'bg-white/40' : 'bg-white/10' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{-- Status Banners (More prominent & integrated) --}}
        <div class="space-y-4 mb-8">
            @if($application->workflow_status === 'submitted' || $application->workflow_status === 'verification')
                <div class="p-5 bg-blue-500/5 border border-blue-500/20 rounded-[2rem] flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-xl shrink-0">📝</div>
                    <div>
                        <p class="text-sm font-black text-blue-700 uppercase tracking-widest">Under Verification</p>
                        <p class="text-xs text-blue-600/70 font-medium leading-relaxed">Your application is currently being reviewed by our document verification team. We will notify you once it proceeds to assessment.</p>
                    </div>
                </div>
            @endif

            @if($application->workflow_status === 'assessed')
                <div class="p-6 bg-orange-500/5 border border-orange-500/20 rounded-[2.5rem] flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-xl shadow-orange-500/5">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-orange-500/10 rounded-[1.5rem] flex items-center justify-center text-2xl shrink-0 shadow-inner">📊</div>
                        <div>
                            <p class="text-base font-black text-orange-700 uppercase tracking-widest">Assessment Ready</p>
                            <p class="text-xs text-orange-600/80 font-bold leading-relaxed">Your business tax and fees have been calculated. Please review the summary below and complete the payment to receive your permit.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('client.payment.show', $application->id) }}" class="px-8 py-3.5 bg-orange-500 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-orange-600 transition-all shadow-xl shadow-orange-500/20 whitespace-nowrap">
                            💳 Proceed to Payment
                        </a>
                        <a href="{{ route('client.payment.success', $application->id) }}" class="p-3 bg-white border border-orange-200 text-orange-600 hover:bg-orange-50 transition-all rounded-2xl shadow-sm tooltip" title="Refresh Status">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </a>
                    </div>
                </div>
            @endif

            @if($application->workflow_status === 'paid')
                <div class="p-5 bg-logo-teal/5 border border-logo-teal/20 rounded-[2rem] flex items-center gap-4 shadow-sm">
                    <div class="w-12 h-12 bg-logo-teal/10 rounded-2xl flex items-center justify-center text-xl shrink-0">⏳</div>
                    <div>
                        <p class="text-sm font-black text-logo-teal uppercase tracking-widest">Pending Final Approval</p>
                        <p class="text-xs text-logo-teal/70 font-medium leading-relaxed">Your payment has been received and verified. The back office is now performing a final review before issuing your official Business Permit.</p>
                    </div>
                </div>
            @endif

            @if(in_array($application->workflow_status, ['paid', 'approved']) && collect($application->installments)->contains('status', 'unpaid'))
                <div class="p-4 bg-yellow/10 border border-yellow/30 rounded-[1.5rem] flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow/20 rounded-xl flex items-center justify-center text-lg shrink-0">⚠️</div>
                    <div>
                        <p class="text-xs font-black text-green uppercase tracking-widest">Future Installments Pending</p>
                        <p class="text-[10px] text-green/70 font-bold leading-relaxed">Remaining payments are required according to the schedule below to keep your permit valid throughout the year.</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT: Details + Documents + Assessment --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Business Info Card --}}
                <div class="bg-white rounded-[2rem] border border-lumot/20 shadow-xl shadow-black/5 overflow-hidden">
                    <div class="px-6 py-4 bg-bluebody/30 border-b border-lumot/10 flex items-center justify-between">
                        <h2 class="text-[10px] font-black text-green uppercase tracking-widest">Primary Business Details</h2>
                        <div class="w-2 h-2 rounded-full bg-logo-green animate-pulse"></div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-6">
                            @foreach([
                                ['Business Name',    $application->business->business_name ?? '—', '🏢'],
                                ['Trade Name',       $application->business->trade_name ?? '—', '🏷️'],
                                ['Registered Owner', ($application->owner->first_name ?? '') . ' ' . ($application->owner->last_name ?? ''), '👤'],
                                ['Tax ID (TIN)',     $application->business->tin_no ?? '—', '📑'],
                                ['Business Type',    $application->business->type_of_business ?? '—', '⚙️'],
                                ['Organization',     $application->business->business_organization ?? '—', '🌐'],
                                ['Business Scale',  $application->business->business_scale ?? '—', '📏'],
                                ['Application Date', $application->created_at->format('M d, Y'), '📅'],
                            ] as [$label, $value, $icon])
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-bluebody/50 rounded-lg flex items-center justify-center text-xs shrink-0">{{ $icon }}</div>
                                    <div class="min-w-0">
                                        <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest">{{ $label }}</p>
                                        <p class="text-sm font-black text-green mt-0.5 truncate">{{ $value ?: '—' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Documents Card --}}
                <div class="bg-white rounded-[2rem] border border-lumot/20 shadow-xl shadow-black/5 overflow-hidden">
                    <div class="px-6 py-4 bg-bluebody/30 border-b border-lumot/10 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="text-[10px] font-black text-green uppercase tracking-widest">Submitted Requirements</h2>
                            <span class="px-2 py-0.5 bg-green/10 text-green text-[9px] font-black rounded-full">{{ $application->documents->count() }} Files</span>
                        </div>
                        @if(in_array($application->workflow_status, ['draft', 'returned']))
                            <a href="{{ route('client.documents.index', $application->id) }}"
                                class="text-[10px] font-black text-logo-teal uppercase tracking-widest hover:underline flex items-center gap-1">
                                Manage Files
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    </div>
                    <div class="p-6">
                        @if($application->documents->isEmpty())
                            <div class="py-8 text-center">
                                <p class="text-sm text-gray/40 font-bold italic">No documents uploaded yet.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($application->documents as $doc)
                                    <div class="flex items-center justify-between p-3.5 bg-bluebody/30 rounded-2xl border border-lumot/10 hover:border-logo-teal/30 transition-all group">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl {{ $doc->isVerified() ? 'bg-green-100/50' : ($doc->isRejected() ? 'bg-red-100/50' : 'bg-white') }} flex items-center justify-center shrink-0 shadow-sm border border-lumot/5">
                                                <svg class="w-5 h-5 {{ $doc->isVerified() ? 'text-green-600' : ($doc->isRejected() ? 'text-red-500' : 'text-logo-teal/50') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[11px] font-black text-green truncate tracking-tight">{{ $doc->type_label }}</p>
                                                <p class="text-[9px] text-gray/50 font-bold uppercase tracking-widest">{{ $doc->file_size_formatted }}</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-1 shrink-0 ml-2">
                                            <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest border {{ $doc->status_color }}">
                                                {{ $doc->status }}
                                            </span>
                                            @if($doc->isRejected() && $doc->rejection_reason)
                                                <span class="text-[8px] text-red-500 font-bold text-right leading-tight max-w-[80px] truncate" title="{{ $doc->rejection_reason }}">{{ $doc->rejection_reason }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Assessment + Payment Schedule Integrated Card --}}
                @if($application->assessment)
                    <div class="bg-white rounded-[2rem] border border-lumot/20 shadow-xl shadow-black/5 overflow-hidden">
                        <div class="px-6 py-4 bg-bluebody/30 border-b border-lumot/10 flex items-center justify-between">
                            <h2 class="text-[10px] font-black text-green uppercase tracking-widest">Financial Assessment Summary</h2>
                            <div class="flex items-center gap-1.5 px-3 py-1 bg-white border border-lumot/10 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                <span class="text-[9px] font-black text-gray uppercase tracking-widest">{{ ucfirst(str_replace('_', ' ', $application->mode_of_payment)) }}</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                {{-- Fees Breakdown --}}
                                <div>
                                    <h3 class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-4">Detailed Breakdown</h3>
                                    <div class="space-y-3">
                                        @foreach($application->assessment->breakdown as $label => $amount)
                                            @if($amount > 0)
                                                <div class="flex justify-between items-center text-xs group">
                                                    <span class="font-bold text-gray/70 group-hover:text-green transition-colors">{{ $label }}</span>
                                                    <span class="font-black text-green">₱ {{ number_format($amount, 2) }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                        <div class="pt-4 mt-2 border-t-2 border-lumot/20">
                                            <div class="flex justify-between items-end">
                                                <div>
                                                    <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest leading-none mb-1">Total Assessment</p>
                                                    <p class="text-2xl font-black text-green leading-none tracking-tighter">{{ $application->assessment->formatted_total }}</p>
                                                </div>
                                                @if(in_array($application->workflow_status, ['assessed']))
                                                    <div class="text-[9px] font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-lg border border-orange-200 animate-pulse">
                                                        Awaiting Payment
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Schedule Table --}}
                                <div>
                                    <h3 class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-4">Payment Schedule</h3>
                                    <div class="space-y-2">
                                        @foreach($application->installments as $inst)
                                            @php
                                                $isPaid = $inst['status'] === 'paid';
                                                $isActive = $inst['status'] === 'unpaid' && ($loop->first || $application->installments[$loop->index-1]['status'] === 'paid');
                                            @endphp
                                            <div class="p-3 rounded-2xl border transition-all flex items-center justify-between
                                                {{ $isPaid ? 'bg-green/5 border-green/10 opacity-70' : ($isActive ? 'bg-white border-orange-500 shadow-sm scale-[1.02]' : 'bg-gray-50/50 border-gray-100 opacity-50') }}">
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $isActive ? 'text-orange-600' : 'text-green' }}">
                                                        {{ $inst['label'] }}
                                                    </span>
                                                    <span class="text-[9px] font-bold text-gray/60 uppercase">{{ $inst['due_date'] }}</span>
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span class="text-xs font-black text-green">₱{{ number_format($inst['amount'], 2) }}</span>
                                                    @if($isPaid)
                                                        @if($inst['bpls_payment_id'])
                                                            <a href="{{ route('client.payment.receipt', ['application' => $application->id, 'payment' => $inst['bpls_payment_id']]) }}" target="_blank"
                                                               class="text-[8px] font-black text-logo-teal uppercase tracking-widest hover:underline mt-0.5">View Receipt</a>
                                                        @else
                                                            <span class="text-[8px] font-black text-logo-green uppercase tracking-widest">Paid</span>
                                                        @endif
                                                    @elseif($isActive)
                                                        <a href="{{ route('client.payment.show', ['application' => $application->id, 'installment' => $inst['number']]) }}"
                                                           class="text-[9px] font-black text-orange-500 uppercase tracking-widest hover:underline mt-1 bg-orange-502 px-2 py-0.5 rounded-md border border-orange-200">Pay Now →</a>
                                                    @else
                                                        <span class="text-[8px] font-black text-gray/30 uppercase tracking-widest">Pending</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- RIGHT: Activity Log --}}
            <div class="space-y-8">
                <div class="bg-white rounded-[2rem] border border-lumot/20 shadow-xl shadow-black/5 overflow-hidden">
                    <div class="px-6 py-4 bg-bluebody/30 border-b border-lumot/10 flex items-center justify-between">
                        <h2 class="text-[10px] font-black text-green uppercase tracking-widest">Activity Timeline</h2>
                        <div class="w-2 h-2 rounded-full bg-logo-teal/40"></div>
                    </div>
                    <div class="p-6">
                        @if($application->activityLogs->isEmpty())
                            <p class="text-[11px] text-gray/40 font-bold italic text-center py-4">No recent activity recorded.</p>
                        @else
                            <div class="relative pl-2">
                                <div class="absolute left-[11px] top-0 bottom-0 w-[2px] bg-lumot/10 rounded-full"></div>
                                <div class="space-y-6">
                                    @foreach($application->activityLogs as $log)
                                        <div class="relative flex gap-4 group">
                                            <div class="w-[24px] h-[24px] rounded-lg bg-white border-2 border-lumot/20 flex items-center justify-center shrink-0 z-10 text-[10px] shadow-sm group-hover:border-logo-teal/30 transition-colors">
                                                {{ $log->action_icon }}
                                            </div>
                                            <div class="flex-1 pb-1">
                                                <div class="flex justify-between items-start gap-2">
                                                    <p class="text-[11px] font-black text-green tracking-tight leading-tight">{{ $log->action_label }}</p>
                                                    <span class="text-[8px] font-black text-gray/30 uppercase tracking-tighter whitespace-nowrap">{{ $log->created_at->diffForHumans(null, true) }}</span>
                                                </div>
                                                <p class="text-[9px] text-gray/50 font-bold uppercase tracking-widest mt-0.5">{{ $log->actor_name }}</p>
                                                @if($log->remarks)
                                                    <div class="mt-2 p-2 bg-bluebody/30 rounded-xl border border-lumot/10">
                                                        <p class="text-[10px] text-green/70 font-medium italic leading-relaxed">{{ $log->remarks }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- App Info Card --}}
                <div class="bg-white rounded-[2rem] border border-lumot/20 shadow-xl shadow-black/5 overflow-hidden">
                    <div class="px-6 py-4 bg-bluebody/30 border-b border-lumot/10">
                        <h2 class="text-[10px] font-black text-green uppercase tracking-widest">Metadata & Tracking</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach([
                            ['Application No.',    $application->application_number, 'green'],
                            ['Submission Type',    ucfirst($application->application_type), 'gray/60'],
                            ['Assessment Year',    $application->permit_year, 'gray/60'],
                            ['Date Registered',    $application->created_at->format('M d, Y'), 'gray/60'],
                            ['Submission Date',  $application->submitted_at?->format('M d, Y') ?? 'Not Submitted', 'gray/60'],
                            ['Verified On',   $application->verified_at?->format('M d, Y') ?? 'Pending', 'gray/60'],
                            ['Approval Date',   $application->approved_at?->format('M d, Y') ?? 'Pending', 'gray/60'],
                        ] as [$label, $value, $color])
                            <div class="flex justify-between items-center group">
                                <span class="text-[9px] font-black text-gray/40 uppercase tracking-widest group-hover:text-gray/60 transition-colors">{{ $label }}</span>
                                <span class="text-[10px] font-black text-{{ $color }}">{{ $value }}</span>
                            </div>
                        @endforeach
                        
                        {{-- QR Placeholder / Internal Link --}}
                        <div class="pt-4 mt-2 border-t border-lumot/10">
                            <div class="p-3 bg-bluebody/40 rounded-2xl flex items-center justify-center gap-2 border border-dashed border-lumot/20">
                                <svg class="w-4 h-4 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span class="text-[9px] font-black text-gray/30 uppercase tracking-widest">Verified Digital Record</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection