@php
    /**
     * Reusable Workflow Steps Visualizer for RPT Properties
     * 
     * @param string $active - 'intake', 'faas', 'td', 'treasury'
     * @param mixed  $record - Either a FaasProperty or TaxDeclaration model
     */
    
    // Determine the state of each major milestone
    $hasFaas      = false;
    $faasApproved = false;
    $hasTd        = false;
    $tdApproved   = false;
    $isForwarded  = false;

    if (isset($record)) {
        if ($record instanceof \App\Models\RPT\FaasProperty) {
            /** @var \App\Models\RPT\FaasProperty $record */
            $hasFaas      = true;
            $faasApproved = $record->status === 'approved';
            
            // Check for associated TD
            $latestTd = $record->taxDeclarations()->latest()->first();
            if ($latestTd) {
                $hasTd      = true;
                $tdApproved = $latestTd->status === 'approved' || $latestTd->status === 'forwarded';
                $isForwarded = $latestTd->status === 'forwarded';
            }
        } elseif ($record instanceof \App\Models\RPT\TaxDeclaration) {
            /** @var \App\Models\RPT\TaxDeclaration $record */
            $hasFaas      = true;
            $faasApproved = true; // TD exists, so FAAS must be approved
            $hasTd        = true;
            $tdApproved   = $record->status === 'approved' || $record->status === 'forwarded';
            $isForwarded  = $record->status === 'forwarded';
        }
    }

    $steps = [
        [
            'id'    => 'intake',
            'label' => 'Intake / Registry',
            'done'  => true, // If we're seeing this, it's registered
            'active'=> $active === 'intake'
        ],
        [
            'id'    => 'faas',
            'label' => 'Appraisal (FAAS)',
            'done'  => $faasApproved,
            'active'=> $active === 'faas'
        ],
        [
            'id'    => 'td',
            'label' => 'Assessment (TD)',
            'done'  => $tdApproved,
            'active'=> $active === 'td'
        ],
        [
            'id'    => 'treasury',
            'label' => 'Treasury Point',
            'done'  => $isForwarded,
            'active'=> $active === 'treasury'
        ],
    ];
@endphp

<div class="bg-white rounded-xl shadow px-6 py-4 mb-4">
    <div class="flex items-center justify-between gap-0">
        @foreach($steps as $i => $step)
            <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                {{-- Step Circle --}}
                <div class="flex flex-col items-center gap-1.5 relative">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-all duration-500
                        {{ $step['done'] ? 'bg-green-500 border-green-500 text-white' : ($step['active'] ? 'bg-blue-50 border-blue-600 text-blue-600 ring-4 ring-blue-50' : 'bg-white border-gray-200 text-gray-400') }}">
                        @if($step['done'])
                            <i class="fas fa-check text-xs"></i>
                        @else
                            <span class="text-xs font-bold">{{ $i + 1 }}</span>
                        @endif
                    </div>
                    
                    <span class="absolute -bottom-6 text-[10px] font-bold uppercase tracking-tight whitespace-nowrap
                        {{ $step['active'] ? 'text-blue-700' : ($step['done'] ? 'text-green-700' : 'text-gray-400') }}">
                        {{ $step['label'] }}
                    </span>
                </div>

                {{-- Connector Line --}}
                @unless($loop->last)
                    <div class="flex-1 h-1 mx-4 rounded-full overflow-hidden bg-gray-100">
                        <div class="h-full transition-all duration-1000 
                            {{ $step['done'] ? 'bg-green-500 w-full' : ($step['active'] ? 'bg-blue-200 w-1/2' : 'w-0') }}">
                        </div>
                    </div>
                @endunless
            </div>
        @endforeach
    </div>
    <div class="h-4"></div> {{-- Spacer for absolute labels --}}
</div>
