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

{{-- 1️⃣ Master Property Header & Workflow --}}
<div class="bg-white rounded-xl shadow mb-4">
    <div class="px-6 py-4 flex flex-wrap items-start justify-between gap-4 border-b">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('rpt.faas.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left text-sm"></i></a>
                <h2 class="text-xl font-bold text-gray-800">{{ $faas->propertyRegistration->owner_name ?? $faas->owner_name }}</h2>
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
            </div>
            <p class="text-xs text-gray-500 font-medium">
                <span class="bg-gray-100 px-2 py-0.5 rounded">ARP: {{ $faas->arp_no ?? 'UNASSIGNED' }}</span>
                <span class="mx-2 text-gray-300">|</span>
                <span>Revision Year: {{ $faas->revision_year_id ?? 'Active' }}</span>
                <span class="mx-2 text-gray-300">|</span>
                <span>Barangay: {{ $faas->barangay?->brgy_name ?? '—' }}</span>
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
                        <div class="absolute right-0 top-full mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 p-3">
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
                <form action="{{ route('rpt.faas.approve', $faas) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Confirming FINAL APPROVAL. This will generate an ARP and lock the record.')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all hover:scale-105">
                        <i class="fas fa-check-circle mr-1.5 opacity-70"></i> Approve & Issue ARP
                    </button>
                </form>
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
                <a href="{{ route('rpt.faas.print-noa', $faas) }}" target="_blank" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all ml-1 flex items-center">
                    <i class="fas fa-envelope-open-text mr-1.5 opacity-70"></i> Print NOA
                </a>
                <div class="relative inline-block text-left ml-1" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
                        <i class="fas fa-plus-circle text-xs"></i> New Transaction <i class="fas fa-chevron-down text-[10px] opacity-70"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden border border-gray-100" 
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
                            <button disabled class="w-full text-left px-4 py-2.5 text-xs font-bold text-gray-400 flex items-center gap-3 uppercase tracking-wider cursor-not-allowed">
                                <i class="fas fa-object-group w-4"></i> Consolidation
                            </button>
                        </div>
                    </div>
                </div>

                
                @if(in_array(auth()->user()->role, ['admin', 'provincial_assessor']))
                    <button onclick="document.getElementById('revokeModal').classList.remove('hidden')" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all ml-2">
                        <i class="fas fa-undo mr-1.5 opacity-70"></i> Revoke Approval
                    </button>
                @endif
            @endif
        </div>
    </div>
    
    {{-- Revoke Approval Modal --}}
    <div id="revokeModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
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
        </div>
    </div>
    
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Owner Address</div>
            <div class="text-sm text-gray-700 leading-relaxed">{{ $faas->propertyRegistration->owner_address ?? $faas->owner_address }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Owner TIN / Contact</div>
            <div class="text-sm text-gray-700">TIN: {{ $faas->propertyRegistration->owner_tin ?? $faas->owner_tin ?? '—' }}</div>
            <div class="text-sm text-gray-700 mt-1">{{ $faas->propertyRegistration->owner_contact ?? $faas->owner_contact ?? 'No Phone' }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Property Location</div>
            <div class="text-sm text-gray-700">{{ $faas->propertyRegistration->street ?? $faas->street ?? 'Street/Sitio' }}</div>
            <div class="text-sm text-gray-700 mt-1">{{ $faas->propertyRegistration->municipality ?? $faas->municipality }}, {{ $faas->propertyRegistration->province ?? $faas->province }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Cadastral Details</div>
            <div class="text-sm text-gray-700">Lot: {{ $faas->propertyRegistration->lot_no ?? $faas->lot_no ?? '—' }} | Blk: {{ $faas->propertyRegistration->blk_no ?? $faas->blk_no ?? '—' }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Survey: {{ $faas->propertyRegistration->survey_no ?? $faas->survey_no ?? '—' }}</div>
        </div>
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Title Reference</div>
            <div class="text-sm font-bold text-blue-700">{{ $faas->propertyRegistration->title_no ?? $faas->title_no ?? 'NO TITLE' }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Property Type: <span class="capitalize">{{ $faas->propertyRegistration->property_type ?? $faas->property_type }}</span></div>
        </div>
    </div>
</div>
