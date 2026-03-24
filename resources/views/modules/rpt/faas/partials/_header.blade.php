{{-- Return Remarks Banner --}}
@if(in_array($faas->status, ['draft', 'for_review']) && !empty($faas->return_remarks))
<div class="bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm mb-4">
    <div class="px-6 py-4 flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-500 text-lg mt-0.5"></i>
        <div>
            <h4 class="text-red-800 font-bold text-sm tracking-tight">Record Returned for Correction</h4>
            <p class="text-sm text-red-700 mt-1">{{ $faas->return_remarks }}</p>
        </div>
    </div>
</div>
@endif

{{-- Reviewer Warnings Banner --}}
@if($faas->status === 'for_review')
    @php $warnings = $faas->reviewerWarnings(); @endphp
    @if(count($warnings) > 0)
    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-xl shadow-sm mb-4">
        <div class="px-6 py-4">
            <h4 class="text-amber-800 font-bold text-sm tracking-tight flex items-center gap-2 mb-2">
                <i class="fas fa-exclamation-triangle mt-0.5"></i> Automated Reviewer Alerts
            </h4>
            <ul class="space-y-1.5">
                @foreach($warnings as $warning)
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-amber-500 mt-1.5"></i>
                        <span class="text-sm text-amber-700 leading-tight">{{ $warning }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
@endif

{{-- Pending Forwarding Warning --}}
@if($faas->isApproved() && !$faas->isForwardedToTreasury())
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-xl shadow-sm mb-4">
    <div class="px-6 py-4 flex items-start gap-3">
        <div class="bg-blue-100 p-2 rounded-lg">
            <i class="fas fa-paper-plane text-blue-600 text-sm"></i>
        </div>
        <div>
            <h4 class="text-blue-800 font-bold text-sm tracking-tight">Property Pending Forwarding to Treasury</h4>
            <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                This record is <strong>locally approved</strong> in the Assessor's office. To initiate new transactions (Transfers, Splits) or accept payments, you must first <strong>Forward</strong> its Tax Declarations (Assessment Stage) to the Treasury.
            </p>
            <div class="mt-3">
                <a href="{{ route('rpt.td.index', ['faas_property_id' => $faas->id]) }}" class="text-[10px] font-bold uppercase tracking-widest bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors shadow-sm inline-flex items-center gap-2">
                    Go to Tax Declarations <i class="fas fa-arrow-right text-[8px]"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- 1️⃣ Master Property Header & Workflow --}}
<div class="bg-white rounded-xl shadow mb-4">
    <div class="px-6 py-4 flex flex-wrap items-start justify-between gap-4 border-b">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('rpt.faas.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left text-sm"></i></a>
                <h2 class="text-xl font-bold text-gray-800">{{ $faas->primary_owner_name }}</h2>
                @php 
                    $badge = match($faas->status) { 
                        'draft' => 'bg-gray-100 text-gray-700', 
                        'for_review' => 'bg-yellow-100 text-yellow-700', 
                        'recommended' => 'bg-blue-100 text-blue-700',
                        'approved' => 'bg-green-100 text-green-700', 
                        'inactive' => 'bg-red-50 text-red-700',
                        default => 'bg-gray-50 text-gray-500' 
                    }; 
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badge }}">
                    {{ str_replace('_',' ',$faas->status) }}
                </span>
                @if($faas->revision_type)
                    @php
                        $revType = trim($faas->revision_type);
                        $revBadge = match(strtolower($revType)) {
                            'general revision' => 'bg-indigo-600 text-white',
                            'transfer', 'transfer ownership' => 'bg-blue-600 text-white',
                            'reassessment' => 'bg-amber-600 text-white',
                            'split', 'subdivision' => 'bg-purple-600 text-white',
                            'consolidation' => 'bg-pink-600 text-white',
                            'new discovery' => 'bg-emerald-600 text-white text-[9px]',
                            default => 'bg-cyan-600 text-white'
                        };
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $revBadge }} shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                        <i class="fas fa-tag text-[8px] opacity-70"></i> {{ $revType }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 font-medium mt-1">
                <span class="bg-gray-100 px-2 py-0.5 rounded">ARP: {{ $faas->arp_no ?? 'UNASSIGNED' }}</span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded">PIN: {{ $faas->pin ?? $faas->generateStructuredPin() }}</span>
                <span class="mx-2 text-gray-300">|</span>
                <span>Revision Year: {{ $faas->revisionYear->revision_year ?? 'Active' }}</span>
                <span class="mx-2 text-gray-300">|</span>
                <span>Barangay: {{ $faas->barangay?->brgy_name ?? '—' }}</span>
                <span class="mx-2 text-gray-300">|</span>
                @if(isset($faas->is_taxable))
                    <span class="{{ $faas->is_taxable ? 'text-blue-600 bg-blue-50' : 'text-emerald-700 bg-emerald-50' }} px-2 py-0.5 rounded uppercase tracking-wider text-[10px] font-bold">
                        {{ $faas->is_taxable ? 'Taxable' : 'Exempt' }}
                    </span>
                    @if(!$faas->is_taxable && $faas->exemption_basis)
                        <span class="ml-1 text-[10px] text-gray-500 italic">({{ $faas->exemption_basis }})</span>
                    @endif
                @else
                    <span class="text-gray-400">Class: Unknown</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2 items-center">
            @if($faas->isDraft() || $faas->status === 'for_review')
                <form action="{{ route('rpt.faas.recompute', $faas) }}" method="POST" class="mr-2 border-r pr-4 border-gray-200">
                    @csrf
                    <button type="submit" onclick="return confirm('Re-compute all component valuations?')" class="text-blue-600 hover:text-blue-800 font-bold text-[11px] uppercase tracking-wider bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors flex items-center shadow-sm">
                        <i class="fas fa-calculator mr-1.5"></i> Re-Compute All
                    </button>
                </form>
            @endif

            @if($faas->isDraft())
                @php
                    $checklist = $faas->completionChecklist();
                    $isReady = count($checklist) === 0;
                @endphp
                
                @if(!$isReady)
                    <div class="relative group cursor-help mr-2">
                        <div class="flex items-center gap-1.5 bg-red-50 text-red-600 px-3 py-1.5 rounded-lg border border-red-200">
                            <i class="fas fa-exclamation-triangle text-red-500"></i>
                            <span class="text-[11px] font-bold uppercase tracking-widest">{{ count($checklist) }} Action(s) Required</span>
                        </div>
                        
                        {{-- Hover dropdown for checklist --}}
                        <div class="absolute right-0 top-full mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all p-3">
                            <div class="text-[10px] font-black uppercase text-gray-400 mb-2 border-b pb-1">Missing Requirements</div>
                            <ul class="space-y-2">
                                @foreach($checklist as $issue)
                                    <li class="flex items-start gap-2 text-xs">
                                        <i class="fas {{ $issue['type'] === 'error' ? 'fa-times-circle text-red-500' : 'fa-info-circle text-amber-500' }} mt-0.5"></i>
                                        <span class="{{ $issue['type'] === 'error' ? 'text-red-700 font-medium' : 'text-gray-600' }} leading-tight tracking-tight">{{ $issue['msg'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                
                <form action="{{ route('rpt.faas.submit-review', $faas) }}" method="POST">
                    @csrf
                    <button type="submit" @if(!$isReady) disabled @endif onclick="return confirm('Submit this record for review?')" class="{{ $isReady ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-300 cursor-not-allowed' }} text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all {{ $isReady ? 'hover:scale-105' : '' }}">
                        <i class="fas fa-paper-plane mr-1.5 opacity-70"></i> Submit for Review
                    </button>
                </form>
            @elseif($faas->status === 'for_review')
                <form action="{{ route('rpt.faas.recommend', $faas) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Recommend this record for final approval?')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all hover:scale-105">
                        <i class="fas fa-thumbs-up mr-1.5 opacity-70"></i> Recommend Approval
                    </button>
                </form>
                <button onclick="document.getElementById('returnModal').classList.remove('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
                    Return to Draft
                </button>
            @elseif($faas->status === 'recommended')
                <a href="{{ route('rpt.faas.preview-td', $faas) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all hover:scale-105">
                    <i class="fas fa-file-invoice mr-1.5 opacity-70"></i> Preview TD
                </a>
                <button onclick="document.getElementById('approveArpModal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all hover:scale-105">
                    <i class="fas fa-check-circle mr-1.5 opacity-70"></i> Approve & Issue ARP
                </button>
                <button onclick="document.getElementById('returnModal').classList.remove('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
                    Return to Draft
                </button>
            @endif
            
            @if($faas->isEditable())
                <button onclick="document.getElementById('editMasterModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all ml-1">
                    <i class="fas fa-edit mr-1.5 opacity-70"></i> Edit Details
                </button>
            @endif
            @if($faas->previous_faas_property_id)
                <a href="{{ route('rpt.faas.compare', $faas) }}" class="bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all ml-1 flex items-center">
                    <i class="fas fa-columns mr-1.5 opacity-70"></i> Compare
                </a>
            @endif
            
            @if($faas->isApproved())
                @php 
                    $isForwarded = $faas->isForwardedToTreasury(); 
                    $isPaid = $faas->isFullyPaid();
                    $canTransact = $isForwarded && $isPaid;
                @endphp
                
                @if(!$canTransact)
                    <div class="group relative inline-block ml-1">
                        <button disabled class="bg-gray-300 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm cursor-not-allowed flex items-center gap-2 opacity-75">
                            <i class="fas fa-lock text-xs opacity-70"></i> New Transaction <i class="fas fa-chevron-down text-[10px] opacity-70"></i>
                        </button>
                        <div class="absolute right-0 bottom-full mb-2 w-64 bg-gray-900 text-white text-[10px] p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-[100] shadow-xl">
                            <div class="font-bold text-amber-400 mb-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i> Workflow Restriction
                            </div>
                            @if(!$isForwarded)
                                New transactions are locked until at least one Tax Declaration is <strong>Forwarded to Treasury</strong> (Assessment Stage).
                            @elseif(!$isPaid)
                                ALL Tax Declarations must be <strong>Fully Paid</strong> (Tax Clearance) before initiating new transactions.
                            @endif
                        </div>
                    </div>
                @else
                    <div class="relative z-50 inline-block text-left ml-1" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
                            <i class="fas fa-plus-circle text-xs"></i> New Transaction <i class="fas fa-chevron-down text-[10px] opacity-70"></i>
                        </button>
                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5  overflow-hidden border border-gray-100" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         style="display: none;">
                        <div class="py-1">
                            <button type="button" @click="document.getElementById('transferModal').classList.remove('hidden'); open = false;" class="w-full text-left px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-blue-50 flex items-center gap-3 transition-colors uppercase tracking-wider">
                                <i class="fas fa-exchange-alt text-blue-500 w-4"></i> Transfer of Ownership
                            </button>
                            <form action="{{ route('rpt.faas.reassess', $faas) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Initiate Reassessment?')" class="w-full text-left px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-blue-50 flex items-center gap-3 transition-colors uppercase tracking-wider">
                                    <i class="fas fa-calculator text-emerald-500 w-4"></i> Reassessment
                                </button>
                            </form>
                            <form action="{{ route('rpt.faas.general-revision', $faas) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Initiate General Revision?')" class="w-full text-left px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-blue-50 flex items-center gap-3 transition-colors uppercase tracking-wider">
                                    <i class="fas fa-history text-amber-500 w-4"></i> General Revision
                                </button>
                            </form>
                            <div class="border-t border-gray-100 my-1"></div>
                            <button type="button" @click="document.getElementById('subdivideModal').classList.remove('hidden'); open = false;" 
                                    class="w-full text-left px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-emerald-50 flex items-center gap-3 transition-colors uppercase tracking-wider">
                                <i class="fas fa-object-ungroup text-emerald-500 w-4"></i> Subdivision
                            </button>
                            <a href="{{ route('rpt.faas.index', ['select_id' => $faas->id]) }}" 
                               class="w-full text-left px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-pink-50 flex items-center gap-3 transition-colors uppercase tracking-wider">
                                <i class="fas fa-object-group text-pink-500 w-4"></i> Consolidation
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                
                @if(in_array(auth()->user()->role, ['admin', 'provincial_assessor']))
                    <button onclick="document.getElementById('revokeModal').classList.remove('hidden')" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all ml-2">
                        <i class="fas fa-undo mr-1.5 opacity-70"></i> Revoke Approval
                    </button>
                @endif
            @endif
        </div>
    </div>
    
    {{-- Revoke Approval Modal --}}
    <div id="revokeModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm  flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 bg-red-600 text-white flex justify-between items-center">
                <h3 class="font-bold text-lg leading-none tracking-tight">Revoke Property Approval</h3>
                <button onclick="document.getElementById('revokeModal').classList.add('hidden')" class="text-red-100 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('rpt.faas.revoke-approval', $faas) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="bg-red-50 p-4 rounded-xl border border-red-100 mb-4">
                    <div class="flex gap-3">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                        <p class="text-xs text-red-800 leading-relaxed">
                            <strong>Warning:</strong> Revoking approval will delete any generated Tax Declarations (TDs) that have not been forwarded to Treasury. This record will be reverted to <strong>For Review</strong> status.
                        </p>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Revocation Reason (Mandatory) *</label>
                    <textarea name="remarks" required minlength="10" class="w-full border-gray-200 border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all min-h-[100px]" placeholder="Explain why this approval is being revoked..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                    <button type="button" onclick="document.getElementById('revokeModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" class="bg-red-600 text-white px-8 py-2.5 rounded-xl hover:bg-red-700 shadow-lg shadow-red-100 transition-all">Confirm Revocation</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        <div class="lg:col-span-1">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 flex justify-between items-center">
                <span>Ownership Details</span>
                <span class="text-[9px] bg-gray-100 px-1 rounded">{{ $faas->owners->count() }}</span>
            </div>
            <div class="space-y-3 max-h-32 overflow-y-auto pr-1 custom-scrollbar">
                @foreach($faas->owners as $owner)
                    <div class="{{ !$loop->first ? 'pt-2 border-t border-dashed border-gray-100' : '' }}">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="text-sm font-bold text-gray-800 tracking-tight leading-none">{{ $owner->owner_name }}</span>
                            @if($owner->is_primary)
                                <span class="bg-blue-50 text-blue-600 text-[8px] font-black uppercase px-1 rounded border border-blue-100 tracking-tighter shadow-sm">Primary</span>
                            @endif
                        </div>
                        <div class="text-[10px] text-gray-500 line-clamp-1" title="{{ $owner->owner_address }}">{{ $owner->owner_address }}</div>
                        <div class="text-[9px] text-gray-400 mt-0.5 font-medium uppercase tracking-wider">
                            TIN: {{ $owner->owner_tin ?: '—' }} | {{ $owner->owner_contact ?: 'NO PHONE' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Administrator details</div>
            <div class="text-sm text-gray-700">{{ $faas->administrator_name ?? '—' }}</div>
            <div class="text-[11px] text-gray-500 mt-2">TIN: {{ $faas->administrator_tin ?? '—' }} | {{ $faas->administrator_contact ?? 'No Phone' }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Property Location</div>
            <div class="text-sm text-gray-700">{{ $faas->propertyRegistration->street ?? $faas->street ?? 'Street/Sitio' }}</div>
            <div class="text-[11px] text-gray-500 mt-1">{{ $faas->propertyRegistration->district ?? $faas->district ?? 'No District' }} | {{ $faas->propertyRegistration->municipality ?? $faas->municipality }}, {{ $faas->propertyRegistration->province ?? $faas->province }}</div>
        </div>
        <div class="lg:col-span-1">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Cadastral Details</div>
            <div class="text-sm text-gray-700">Lot: <span id="header-lot-no">{{ $faas->lot_no ?? $faas->parentLand->lot_no ?? $faas->propertyRegistration->lot_no ?? '—' }}</span> | Blk: <span id="header-blk-no">{{ $faas->blk_no ?? $faas->parentLand->blk_no ?? $faas->propertyRegistration->blk_no ?? '—' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">Survey: <span id="header-survey-no">{{ $faas->survey_no ?? $faas->parentLand->survey_no ?? $faas->propertyRegistration->survey_no ?? '—' }}</span></div>
        </div>
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Title Reference</div>
            <div class="text-sm font-bold text-blue-700" id="header-title-no">{{ $faas->title_no ?? $faas->propertyRegistration->title_no ?? 'No Title' }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Property Type: <span class="capitalize" id="header-property-type">{{ $faas->property_type ?? $faas->propertyRegistration->property_type }}</span></div>
        </div>
        <div class="lg:col-span-1">
            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1.5 flex items-center gap-1 relative z-10">
                <i class="fas fa-map-marked-alt"></i> Spatial Overview
            </div>
            <div id="headerSpatialMap" class="w-full h-16 rounded-lg border border-emerald-100 bg-gray-50 flex items-center justify-center overflow-hidden z-0 relative transition-all duration-300 transform hover:scale-[1.02]">
                <span class="text-[10px] text-gray-400 italic">No coordinates</span>
            </div>
        </div>
    </div>
    <!-- Approve & Issue ARP Modal -->
    <div id="approveArpModal" class="fixed inset-0 bg-black/50 z-50 {{ ($errors->has('manual_arp_no') || $errors->has('manual_td_no') || old('manual_arp_no')) ? '' : 'hidden' }} flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-green-50 border-b flex justify-between items-center">
                <h3 class="text-sm font-bold text-green-800"><i class="fas fa-check-circle mr-2 opacity-70"></i> Approve & Issue ARP</h3>
                <button onclick="document.getElementById('approveArpModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times"></i></button>
            </div>
            
            @php
                $dynamicArp = \App\Models\RPT\FaasProperty::generateArpNo($faas);
                $dynamicTd  = \App\Models\RPT\TaxDeclaration::generateTdNo();
            @endphp

            <form action="{{ route('rpt.faas.approve', $faas) }}" method="POST" class="p-6 space-y-4">
                @csrf

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-xs flex items-start gap-3 mb-2 animate-pulse">
                        <i class="fas fa-exclamation-triangle mt-0.5"></i>
                        <div>
                            <span class="font-bold">Approval Failed:</span>
                            <p class="mt-1">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(in_array($faas->revision_type, ['General Revision', 'Reassessment']) && $faas->previous_assessed_value > 0)
                    @php
                        $isGR = $faas->revision_type === 'General Revision';
                        $themeColor = $isGR ? 'indigo' : 'blue';
                        $label = $isGR ? 'General Revision' : 'Reassessment';
                        $icon = $isGR ? 'calculator' : 'sync-alt';
                    @endphp
                    <div class="bg-{{ $themeColor }}-50/50 border border-{{ $themeColor }}-100 rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between text-[10px] font-bold text-{{ $themeColor }}-400 uppercase mb-3">
                            <span class="flex items-center gap-1.5"><i class="fas fa-{{ $icon }} text-{{ $themeColor }}-300"></i> Valuation Change</span>
                            <span class="px-2 py-0.5 bg-{{ $themeColor }}-100 text-{{ $themeColor }}-600 rounded-full">{{ $label }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase mb-1">Previous Assessed Value</p>
                                <p class="text-sm font-black text-gray-500">₱ {{ number_format($faas->previous_assessed_value, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-{{ $themeColor }}-400 uppercase mb-1">New Assessed Value</p>
                                <p class="text-sm font-black text-{{ $themeColor }}-700">₱ {{ number_format($faas->total_assessed_value, 2) }}</p>
                            </div>
                        </div>
                        @php
                            $diff = $faas->total_assessed_value - $faas->previous_assessed_value;
                            $pct = ($faas->previous_assessed_value > 0) ? ($diff / $faas->previous_assessed_value) * 100 : 0;
                        @endphp
                        <div class="mt-3 pt-3 border-t border-{{ $themeColor }}-100/50 flex items-center justify-between">
                             <span class="text-[10px] font-bold text-{{ $themeColor }}-400/70 uppercase">Total Variance:</span>
                             <span class="text-xs font-black px-2 py-1 rounded-lg {{ $diff >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                <i class="fas fa-arrow-{{ $diff >= 0 ? 'up' : 'down' }} mr-1 text-[10px]"></i>
                                {{ number_format(abs($pct), 1) }}%
                             </span>
                        </div>
                    </div>
                @endif

                <div class="bg-blue-50 text-blue-800 text-xs p-3 rounded-lg border border-blue-100 mb-4">
                    <i class="fas fa-info-circle mr-1"></i> You can use the auto-generated ARP Number or input your own.
                </div>

                <input type="hidden" name="manual_override" value="1">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex justify-between items-center">
                        <span>Issue ARP No. <span class="text-red-500">*</span></span>
                        <button type="button" onclick="refreshArpAndTd()" class="text-[9px] text-blue-600 font-black hover:text-blue-800 transition-colors flex items-center gap-1 uppercase tracking-tighter">
                            <i class="fas fa-sync-alt"></i> Force Generate
                        </button>
                    </label>
                    <div class="relative group">
                        <input type="text" name="manual_arp_no" id="approve_modal_arp" value="{{ old('manual_arp_no', $dynamicArp) }}" required 
                               class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm font-bold text-gray-700 bg-gray-50 uppercase shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <i class="fas fa-magic"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex justify-between items-center">
                        <span>Issue TD No. (Series) <span class="text-red-500">*</span></span>
                    </label>
                    <div class="relative group">
                        <input type="text" name="manual_td_no" id="approve_modal_td" value="{{ old('manual_td_no', $dynamicTd) }}" required 
                               class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm font-bold text-gray-700 bg-gray-50 uppercase shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <i class="fas fa-magic"></i>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" onclick="document.getElementById('approveArpModal').classList.add('hidden')" class="px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200 shadow-sm">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-md transition-all">Confirm Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    async function refreshArpAndTd() {
        const btn = event?.currentTarget;
        if (btn) {
            const icon = btn.querySelector('i');
            if (icon) icon.classList.add('animate-spin');
            btn.disabled = true;
        }

        try {
            const response = await fetch('{{ route("rpt.faas.generate-arp-td", $faas) }}');
            if (!response.ok) throw new Error('API Error');
            const data = await response.json();

            if (data.arp_no) {
                document.getElementById('approve_modal_arp').value = data.arp_no;
            }
            if (data.td_no) {
                document.getElementById('approve_modal_td').value = data.td_no;
            }
            
            // Subtle flash effect
            [document.getElementById('approve_modal_arp'), document.getElementById('approve_modal_td')].forEach(el => {
                if (!el) return;
                el.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
                setTimeout(() => el.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50'), 500);
            });

        } catch (error) {
            console.error('Failed to generate numbers:', error);
            alert('Failed to generate fresh numbers. Please try again or type manually.');
        } finally {
            if (btn) {
                const icon = btn.querySelector('i');
                if (icon) icon.classList.remove('animate-spin');
                btn.disabled = false;
            }
        }
    }
</script>


