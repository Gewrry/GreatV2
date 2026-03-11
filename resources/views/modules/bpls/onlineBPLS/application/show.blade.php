<x-admin.app>

    @php
        /** @var \App\Models\onlineBPLS\BplsOnlineApplication $application */
        $status = $application->workflow_status;
        $requiredMet = $requiredMet ?? false;
        $signatories = $signatories ?? collect();
    @endphp

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            rejectDocId: null,
            rejectDocName: '',
            showReturn: false,
            showReject: false,
            showAssess: false,
            showPaid: false,
            showFinalApprove: false,
            showEditOrs: false,
            selectedInstallment: 1,
            docs: @js($application->documents->mapWithKeys(fn($d) => [$d->id => ['status' => $d->status, 'rejection_reason' => $d->rejection_reason, 'type' => $d->document_type]])),
            verifiedCount: {{ $application->documents->where('status', 'verified')->count() }},
            totalCount: {{ $application->documents->count() }},
            requiredTypes: @js(\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES),
            orNumbers: @js($application->orAssignments->pluck('or_number', 'installment_number')->toArray()),
            userAssignments: @js($userAssignments ?? []),
            
            get requiredVerified() {
                const reqDocs = Object.values(this.docs).filter(d => this.requiredTypes.includes(d.type));
                const verifiedReq = reqDocs.filter(d => d.status === 'verified');
                return verifiedReq.length >= this.requiredTypes.length;
            },

            async verifyDoc(id) {
                try {
                    const res = await fetch(`/bpls/online/documents/${id}/verify`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        if (this.docs[id].status !== 'verified') this.verifiedCount++;
                        this.docs[id].status = 'verified';
                        this.docs[id].rejection_reason = null;
                    }
                } catch (e) { console.error(e); }
            },

            async submitRejectDoc() {
                const reason = document.getElementById('reject-doc-reason-field').value;
                if (!reason) return;
                try {
                    const res = await fetch(`/bpls/online/documents/${this.rejectDocId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ rejection_reason: reason })
                    });
                    const data = await res.json();
                    if (data.success) {
                        if (this.docs[this.rejectDocId].status === 'verified') this.verifiedCount--;
                        this.docs[this.rejectDocId].status = 'rejected';
                        this.docs[this.rejectDocId].rejection_reason = reason;
                        this.rejectDocId = null; 
                    }
                } catch (e) { console.error(e); }
            },

            onRangeChange(rangeId, installmentNum) {
                const range = this.userAssignments.find(r => r.id == rangeId);
                if (range && range.next_or) {
                    this.orNumbers[installmentNum] = range.next_or;
                }
            },
            openRejectDoc(id, name) { 
                this.rejectDocId = id; 
                this.rejectDocName = name;
            }
        }">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-6 shadow-sm border border-lumot/20">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Header Section --}}
                @include('modules.bpls.onlineBPLS.application.partials.header')

                {{-- Workflow Progress Tracker --}}
                @include('modules.bpls.onlineBPLS.application.partials.workflow-progress')

                {{-- Remarks Banner for Returned/Rejected --}}
                @if (in_array($status, ['returned', 'rejected']) && $application->remarks)
                    <div class="mb-5 p-4 {{ $status === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-yellow/10 border-yellow/30' }} border rounded-xl">
                        <p class="text-xs font-bold {{ $status === 'rejected' ? 'text-red-600' : 'text-green' }} uppercase tracking-wider mb-1">
                            {{ $status === 'rejected' ? 'Rejection Reason' : 'Remarks Sent to Client' }}
                        </p>
                        <p class="text-sm {{ $status === 'rejected' ? 'text-red-700' : 'text-green' }}">{{ $application->remarks }}</p>
                    </div>
                @endif

                {{-- Main Content Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    {{-- Left Column: Data --}}
                    <div class="lg:col-span-3 space-y-6">
                        @include('modules.bpls.onlineBPLS.application.partials.owner-info')
                        @include('modules.bpls.onlineBPLS.application.partials.business-info')
                        @include('modules.bpls.onlineBPLS.application.partials.assessment-info')
                        @include('modules.bpls.onlineBPLS.application.partials.permit-info')
                    </div>

                    {{-- Right Column: Documents & Logs --}}
                    <div class="lg:col-span-2 space-y-6">
                        @include('modules.bpls.onlineBPLS.application.partials.documents')
                        @include('modules.bpls.onlineBPLS.application.partials.activity-log')
                    </div>
                </div>
            </div>

            {{-- Modals --}}
            @include('modules.bpls.onlineBPLS.application.partials.modals.assess')
            @include('modules.bpls.onlineBPLS.application.partials.modals.return')
            @include('modules.bpls.onlineBPLS.application.partials.modals.reject-app')
            @include('modules.bpls.onlineBPLS.application.partials.modals.edit-ors')
            @include('modules.bpls.onlineBPLS.application.partials.modals.mark-paid')
            @include('modules.bpls.onlineBPLS.application.partials.modals.final-approve')

            {{-- Modal: Reject Document (Special Case because of textarea ID) --}}
            <div x-show="rejectDocId !== null" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div @click.outside="rejectDocId = null" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20 overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <h3 class="text-sm font-black text-green uppercase tracking-widest">Reject Document</h3>
                    </div>
                    <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">
                        Rejecting: <span class="font-black text-green" x-text="rejectDocName"></span>.
                    </p>
                    <textarea id="reject-doc-reason-field" rows="4" required placeholder="Explain why this document is being rejected..." class="w-full text-sm border border-lumot/30 rounded-2xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 resize-none placeholder-gray/30 mb-5 transition-all"></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="rejectDocId = null" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                        <button type="button" @click="submitRejectDoc()" class="px-5 py-2.5 text-xs font-black bg-red-500 text-white uppercase tracking-widest rounded-2xl hover:bg-red-600 transition-all shadow-lg shadow-red-500/20 hover:shadow-xl">Reject</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-admin.app>