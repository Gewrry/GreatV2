<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')
            
            <div class="bg-white rounded-xl shadow mb-6">
                <div class="px-6 py-4 border-b flex justify-between items-center flex-wrap gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Property Registration #{{ $registration->id }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $registration->status === 'registered' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ strtoupper($registration->status) }}
                        </span>
                    </div>
                        @if($registration->status === 'registered')
                            <button type="button" onclick="openArchiveModal()" class="px-3 py-1.5 border rounded border-red-300 text-red-600 hover:bg-red-50 text-sm">
                                <i class="fas fa-archive mr-1"></i> Archive
                            </button>
                        @endif

                        @if($registration->faasProperties->isEmpty() && $registration->status !== 'archived')
                            <a href="{{ route('rpt.faas.start', [$registration, $registration->property_type]) }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm shadow-sm font-bold flex items-center gap-2">
                                Start Appraisal <i class="fas fa-arrow-right"></i>
                            </a>
                        @elseif($registration->status === 'archived')
                             <span class="px-3 py-1.5 bg-gray-200 text-gray-600 rounded text-sm font-bold">
                                <i class="fas fa-lock mr-1"></i> ARCHIVED
                            </span>
                        @else
                            {{-- Already has FAAS records --}}
                            @foreach($registration->faasProperties as $fp)
                                <a href="{{ route('rpt.faas.show', $fp) }}"
                                   class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm shadow-sm font-medium">
                                    <i class="fas fa-file-alt mr-1"></i> View FAAS #{{ $fp->id }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Owner Information</h3>
                        <dl class="text-sm space-y-2">
                            <div class="flex"><dt class="w-32 text-gray-500">Name:</dt><dd class="font-medium text-gray-800">{{ $registration->owner_name }}</dd></div>
                            <div class="flex"><dt class="w-32 text-gray-500">Address:</dt><dd class="text-gray-800">{{ $registration->owner_address }}</dd></div>
                            <div class="flex"><dt class="w-32 text-gray-500">TIN:</dt><dd class="text-gray-800">{{ $registration->owner_tin ?: '-' }}</dd></div>
                            <div class="flex"><dt class="w-32 text-gray-500">Contact:</dt><dd class="text-gray-800">{{ $registration->owner_contact ?: '-' }}</dd></div>
                        </dl>
                        
                        @if($registration->administrator_name)
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-6 mb-3">Administrator</h3>
                        <dl class="text-sm space-y-2">
                            <div class="flex"><dt class="w-32 text-gray-500">Name:</dt><dd class="font-medium text-gray-800">{{ $registration->administrator_name }}</dd></div>
                            <div class="flex"><dt class="w-32 text-gray-500">Address:</dt><dd class="text-gray-800">{{ $registration->administrator_address }}</dd></div>
                        </dl>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Property Details</h3>
                        <dl class="text-sm space-y-2">
                            <div class="flex"><dt class="w-32 text-gray-500">Type:</dt><dd class="font-medium capitalize text-gray-800">{{ $registration->property_type }}</dd></div>
                            <div class="flex"><dt class="w-32 text-gray-500">Location:</dt><dd class="text-gray-800">{{ $registration->full_address }}</dd></div>
                            <div class="flex"><dt class="w-32 text-gray-500">Title No:</dt><dd class="text-gray-800">{{ $registration->title_no ?: '-' }}</dd></div>
                            <div class="flex"><dt class="w-32 text-gray-500">Lot & Blk:</dt>
                                <dd class="text-gray-800">
                                    {{ $registration->lot_no ? 'Lot '.$registration->lot_no : '' }} 
                                    {{ $registration->blk_no ? 'Blk '.$registration->blk_no : '' }}
                                    {{ !$registration->lot_no && !$registration->blk_no ? '-' : '' }}
                                </dd>
                            </div>
                            <div class="flex"><dt class="w-32 text-gray-500">Survey No:</dt><dd class="text-gray-800">{{ $registration->survey_no ?: '-' }}</dd></div>
                            
                            @if($registration->estimated_floor_area)
                                <div class="flex mt-2 pt-2 border-t border-gray-100"><dt class="w-32 text-gray-500">Est. Floor Area:</dt><dd class="font-medium text-gray-800">{{ number_format($registration->estimated_floor_area, 2) }} sqm</dd></div>
                            @endif
                            @if($registration->machinery_description)
                                <div class="flex mt-2 pt-2 border-t border-gray-100"><dt class="w-32 text-gray-500">Description:</dt><dd class="font-medium text-gray-800">{{ $registration->machinery_description }}</dd></div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            </div>

            <div class="bg-white rounded-xl shadow mb-6">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Supporting Documents</h3>
                    <span class="text-xs text-gray-400">{{ $registration->attachments->count() }} file(s) attached</span>
                </div>
                <div class="p-0">
                    @if($registration->attachments->isEmpty())
                        <div class="p-8 text-center text-gray-400 italic text-sm">
                            No documents were uploaded during intake.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                            @foreach($registration->attachments as $file)
                                <div class="border rounded-lg p-3 flex items-start gap-3 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                                        @php
                                            $ext = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                            $icon = match($ext) {
                                                'pdf' => 'fa-file-pdf',
                                                'jpg', 'jpeg', 'png' => 'fa-file-image',
                                                default => 'fa-file'
                                            };
                                        @endphp
                                        <i class="fas {{ $icon }} text-xl"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-gray-500 uppercase tracking-tight">{{ str_replace('_', ' ', $file->type) }}</div>
                                        <div class="text-sm font-medium text-gray-800 truncate" title="{{ $file->label ?: $file->original_filename }}">
                                            {{ $file->label ?: $file->original_filename }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-2">
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline font-bold">
                                                <i class="fas fa-external-link-alt mr-0.5"></i> Open
                                            </a>
                                            <span class="text-[10px] text-gray-400">•</span>
                                            <span class="text-[10px] text-gray-400">By {{ $file->uploadedBy?->name ?? 'System' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-bold text-gray-800">Associated FAAS Records</h3>
                </div>
                <div class="p-0">
                    @if($registration->faasProperties->isEmpty())
                        <div class="p-8 text-center text-gray-500 border-b">
                            <i class="fas fa-file-invoice text-3xl mb-3 text-gray-300"></i>
                            <p>No FAAS records have been drafted for this registration yet.</p>
                            <p class="text-xs text-gray-400 mt-2">Use the <strong>Appraise</strong> buttons above to begin the appraisal process.</p>
                        </div>
                    @else
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-3 font-medium">ARP No.</th>
                                    <th class="px-6 py-3 font-medium">Type</th>
                                    <th class="px-6 py-3 font-medium">Effectivity</th>
                                    <th class="px-6 py-3 font-medium">Revision Type</th>
                                    <th class="px-6 py-3 font-medium">Status</th>
                                    <th class="px-6 py-3 font-medium text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($registration->faasProperties as $faas)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-blue-600">
                                            <a href="{{ route('rpt.faas.show', $faas) }}">{{ $faas->arp_no ?? '(Draft)' }}</a>
                                        </td>
                                        <td class="px-6 py-4 uppercase text-xs font-bold text-gray-500">
                                            {{ $faas->property_type }}
                                        </td>
                                        <td class="px-6 py-4">{{ $faas->effectivity_date ? $faas->effectivity_date->format('M Y') : '-' }}</td>
                                        <td class="px-6 py-4">{{ $faas->revision_type }}</td>
                                        <td class="px-6 py-4">
                                            @include('components.rpt.status-badge', ['status' => $faas->status])
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('rpt.faas.show', $faas) }}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i> View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            @if($registration->remarks)
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <h4 class="text-xs font-bold text-yellow-800 uppercase tracking-widest mb-2">History / Remarks</h4>
                    <div class="text-sm text-yellow-900 whitespace-pre-wrap font-mono leading-relaxed">{{ $registration->remarks }}</div>
                </div>
            @endif
            
        </div>
    </div>

    {{-- Archive Modal --}}
    <div id="archiveModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800">Archive Registration #{{ $registration->id }}</h3>
                <button onclick="closeArchiveModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('rpt.registration.archive', $registration) }}" method="POST" class="p-6">
                @csrf
                <p class="text-sm text-gray-600 mb-4">
                    Archiving will mark this record as inactive. This is typically done for erroneous, duplicate, or cancelled registrations.
                </p>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Archiving <span class="text-red-500">*</span></label>
                    <textarea name="remarks" rows="3" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-300" placeholder="Describe why this registration is being archived..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeArchiveModal()" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg text-sm font-medium shadow-sm">
                        Confirm Archive
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openArchiveModal() {
            document.getElementById('archiveModal').classList.remove('hidden');
            document.getElementById('archiveModal').classList.add('flex');
        }
        function closeArchiveModal() {
            document.getElementById('archiveModal').classList.remove('flex');
            document.getElementById('archiveModal').classList.add('hidden');
        }
    </script>
</x-admin.app>
