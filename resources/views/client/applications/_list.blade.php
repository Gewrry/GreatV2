@if($applications->isEmpty())
    <div class="py-24 text-center bg-white rounded-3xl border border-lumot/10 shadow-sm">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-bluebody/30 mb-6">
            <svg class="w-10 h-10 text-logo-teal/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <p class="text-xl font-black text-green tracking-tight">No applications yet</p>
        <p class="text-sm text-gray/50 mt-1 max-w-xs mx-auto font-medium">When you apply for a business permit, they will appear here for you to track.</p>
        <a href="{{ route('client.apply') }}" class="mt-8 inline-flex items-center gap-2 text-logo-teal font-black text-xs uppercase tracking-widest hover:text-green transition-colors">
            Start your first application
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach($applications as $app)
            @php
                $stages = ['draft', 'submitted', 'verification', 'assessment', 'payment', 'approved'];
                $current = array_search($app->workflow_status, $stages);
                $pct = $current === false ? 0 : (int) (($current / (count($stages) - 1)) * 100);
                if ($app->workflow_status === 'rejected') $pct = 100;

                $statusColors = [
                    'approved' => 'bg-logo-green/10 text-logo-green border-logo-green/20 ring-logo-green/10',
                    'rejected' => 'bg-red-50 text-red-600 border-red-200 ring-red-500/10',
                    'assessed' => 'bg-orange-50 text-orange-600 border-orange-200 ring-orange-500/10',
                    'returned' => 'bg-amber-50 text-amber-600 border-amber-200 ring-amber-500/10',
                    'retirement_requested' => 'bg-orange-50 text-orange-600 border-orange-200 ring-orange-500/10',
                    'retired' => 'bg-gray-100 text-gray-700 border-gray-200 ring-gray-500/10',
                ];
                $sc = $statusColors[$app->workflow_status] ?? 'bg-blue-50 text-blue-600 border-blue-200 ring-blue-500/10';
            @endphp
            
            <div class="group relative bg-white rounded-3xl border border-lumot/20 shadow-sm hover:shadow-xl hover:shadow-logo-teal/5 transition-all duration-300 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-start gap-5 min-w-0">
                            {{-- Visual Indicator --}}
                            <div class="w-14 h-14 rounded-2xl shrink-0 flex items-center justify-center bg-bluebody/30 border border-bluebody/50 text-logo-blue group-hover:scale-105 transition-transform duration-500">
                                @if($app->workflow_status === 'approved')
                                    <svg class="w-7 h-7 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($app->workflow_status === 'rejected')
                                    <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($app->workflow_status === 'assessed')
                                    <svg class="w-7 h-7 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                @elseif($app->workflow_status === 'retired')
                                    <svg class="w-7 h-7 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364a9 9 0 005.636 5.636m12.728 12.728a9 9 0 015.636 5.636m12.728 12.728l5.636 5.636" />
                                    </svg>
                                @elseif($app->workflow_status === 'retirement_requested')
                                    <svg class="w-7 h-7 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @else
                                    <svg class="w-7 h-7 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1.5">
                                    <span class="text-[11px] font-black tracking-widest text-logo-teal bg-logo-teal/5 px-2 py-0.5 rounded border border-logo-teal/10 uppercase">{{ $app->application_number }}</span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border ring-1 {{ $sc }} whitespace-nowrap shadow-sm">
                                        {{ $app->status_label }}
                                    </span>
                                </div>
                                <h3 class="text-xl font-black text-green leading-tight tracking-tight mt-1 group-hover:text-logo-teal transition-colors truncate">
                                    {{ $app->business->business_name ?? 'Untitled Business' }}
                                </h3>
                                <div class="mt-1 flex flex-col gap-0.5">
                                    @php
                                        $stageDesc = match($app->workflow_status) {
                                            'submitted', 'verification' => 'Your application is under document verification.',
                                            'assessed' => 'Assessment ready. Please review fees and pay.',
                                            'paid' => 'Payment received. Pending final approval.',
                                            'approved' => 'Permit issued. You may now download your permit.',
                                            'returned' => 'Action required. Please check remarks.',
                                            'rejected' => 'Application was not approved.',
                                            'retirement_requested' => 'Retirement requested. Awaiting office approval.',
                                            'retired' => 'This business is officially retired.',
                                            default => ''
                                        };
                                    @endphp
                                    @if($stageDesc)
                                        <p class="text-[11px] font-semibold text-gray/70">{{ $stageDesc }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 mt-3 flex-wrap">
                                    <div class="flex items-center gap-1.5 text-[10px] font-black text-gray/50 uppercase tracking-widest">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-10V4m0 10V4m0 4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        {{ $app->application_type }}
                                    </div>
                                    <span class="w-1 h-1 rounded-full bg-lumot/30"></span>
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray/50 uppercase tracking-widest">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Permit Year {{ $app->permit_year }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 md:flex-col lg:flex-row">
                            <form action="{{ route('client.applications.destroy', $app->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this application? This action cannot be undone.')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-5 py-2.5 text-red-500 hover:bg-red-50 rounded-xl border border-red-200 transition-all shadow-sm flex items-center gap-2 text-[11px] font-black uppercase tracking-widest" title="Delete Application">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>

                            @if($app->workflow_status === 'draft')
                                <a href="{{ route('client.applications.edit', $app->id) }}"
                                   class="px-5 py-2.5 bg-white text-logo-teal text-[11px] font-black uppercase tracking-widest rounded-xl border border-logo-teal/30 hover:bg-logo-teal/5 transition-all shadow-sm">
                                    Edit Application
                                </a>
                                <a href="{{ route('client.documents.index', $app->id) }}"
                                   class="px-5 py-2.5 bg-logo-teal text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-green transform hover:-translate-y-0.5 active:translate-y-0 transition-all shadow-lg shadow-logo-teal/20">
                                    Upload Documents
                                </a>
                            @elseif($app->workflow_status === 'returned')
                                <a href="{{ route('client.applications.edit', $app->id) }}"
                                   class="px-6 py-2.5 bg-amber-500 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-600 transform hover:-translate-y-0.5 active:translate-y-0 transition-all shadow-lg shadow-amber-500/20 flex items-center gap-2">
                                    Edit & Resubmit
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            @elseif($app->workflow_status === 'assessed')
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('client.payment.show', $app->id) }}"
                                       class="px-8 py-2.5 bg-orange-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-orange-700 transform hover:-translate-y-0.5 active:translate-y-0 transition-all shadow-lg shadow-orange-600/20 text-center">
                                        Proceed to Payment
                                    </a>
                                    <a href="{{ route('client.payment.success', $app->id) }}"
                                       class="text-center px-4 py-1.5 bg-white border border-orange-200 text-orange-600 text-[9px] font-bold rounded-lg hover:bg-orange-50 transition-colors">
                                        🔄 Refresh Status
                                    </a>
                                </div>
                            @elseif(in_array($app->workflow_status, ['approved', 'approved_for_renewal']))
                                <a href="{{ route('client.applications.show', $app->id) }}"
                                   class="px-5 py-2.5 bg-logo-green text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-green transform hover:-translate-y-0.5 active:translate-y-0 transition-all shadow-lg shadow-logo-green/20">
                                    View Digital Permit
                                </a>
                                @if($app->workflow_status === 'approved_for_renewal')
                                    @php $hasBalance = $app->outstanding_balance > 0.01; @endphp
                                    <a href="{{ $hasBalance ? 'javascript:void(0)' : route('client.applications.renew', $app->id) }}"
                                       class="px-5 py-2.5 {{ $hasBalance ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-blue-600 text-white shadow-lg shadow-blue-200 hover:bg-blue-700' }} text-[11px] font-black uppercase tracking-widest rounded-xl transition-all tooltip"
                                       title="{{ $hasBalance ? 'Please settle your outstanding balance of ₱' . number_format($app->outstanding_balance, 2) . ' to proceed with renewal.' : 'Start Renewal Application' }}">
                                        Start Renewal Application
                                    </a>
                                @elseif(in_array($app->workflow_status, ['approved', 'paid', 'assessed']))
                                    @php $hasBalance = $app->outstanding_balance > 0.01; @endphp
                                    <a href="{{ $hasBalance ? 'javascript:void(0)' : route('client.applications.renew', $app->id) }}"
                                       class="px-5 py-2.5 {{ $hasBalance ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-logo-teal border-logo-teal/30 hover:bg-bluebody/30' }} text-[11px] font-black uppercase tracking-widest rounded-xl border transition-all tooltip"
                                       title="{{ $hasBalance ? 'Please settle your outstanding balance of ₱' . number_format($app->outstanding_balance, 2) . ' to request renewal.' : 'Request renewal for this business' }}">
                                        Renew Permit
                                    </a>
                                @endif

                                @if(in_array($app->workflow_status, ['approved', 'paid', 'assessed', 'approved_for_renewal']))
                                    @php $canRetire = $app->outstanding_balance <= 0.01; @endphp
                                    <a href="{{ $canRetire ? route('client.applications.retire.form', $app->id) : 'javascript:void(0)' }}" 
                                       class="p-2.5 {{ $canRetire ? 'bg-red-50 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-gray-50 text-gray-400 cursor-not-allowed' }} transition-all rounded-xl border border-red-200 shadow-sm tooltip" 
                                       title="{{ $canRetire ? 'Retire Business' : 'Settle balance of ₱' . number_format($app->outstanding_balance, 2) . ' to retire' }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                    </a>
                                @endif
                            @elseif($app->workflow_status === 'renewal_requested')
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-5 py-2.5 bg-blue-50 text-blue-600 text-[11px] font-black uppercase tracking-widest rounded-xl border border-blue-200 shadow-sm flex items-center gap-2">
                                         <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                         </svg>
                                         Renewal Pending Approval
                                    </span>
                                    <span class="text-[10px] text-gray/60 italic">Please wait for back-office validation</span>
                                </div>
                            @elseif($app->workflow_status === 'retirement_requested')
                                <span class="px-5 py-2.5 bg-orange-50 text-orange-600 text-[11px] font-black uppercase tracking-widest rounded-xl border border-orange-200 shadow-sm flex items-center gap-2">
                                     <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Pending Retirement
                                </span>
                            @elseif($app->workflow_status === 'retired')
                                <a href="{{ route('client.applications.retirement-certificate', $app->id) }}"
                                   target="_blank"
                                   class="px-5 py-2.5 bg-white text-orange-600 text-[11px] font-black uppercase tracking-widest rounded-xl border border-orange-200 hover:bg-orange-50 transition-all shadow-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Cert.
                                </a>
                            @else
                                <a href="{{ route('client.applications.show', $app->id) }}"
                                   class="px-6 py-2.5 bg-bluebody/50 text-logo-blue text-[11px] font-black uppercase tracking-widest rounded-xl border border-bluebody hover:bg-bluebody/80 transition-all">
                                    Track Status
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="mt-8 flex flex-col gap-2">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-gray/40">
                            <span>Application Progress</span>
                            <span class="text-logo-teal tabular-nums">{{ $pct }}% Complete</span>
                        </div>
                        <div class="w-full bg-bluebody/30 rounded-full h-2 overflow-hidden border border-bluebody/50">
                            <div class="h-full rounded-full transition-all duration-1000 shadow-[0_0_12px_rgba(0,169,157,0.3)]
                                        {{ in_array($app->workflow_status, ['approved', 'renewal_requested', 'approved_for_renewal']) ? 'bg-logo-green' :
                                           ($app->workflow_status === 'rejected' ? 'bg-red-400' : 'bg-logo-teal') }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination Bar --}}
    @if(method_exists($applications, 'hasPages') && $applications->hasPages())
        <div class="mt-12 py-6 border-t border-lumot/10 ajax-pagination flex justify-center">
            {{ $applications->links() }}
        </div>
    @endif
@endif