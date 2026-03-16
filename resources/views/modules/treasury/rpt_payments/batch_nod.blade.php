{{-- resources/views/modules/treasury/rpt_payments/batch_nod.blade.php --}}
<x-admin.app>
    <style>
        @media print {
            .page-break {
                page-break-after: always;
                clear: both;
            }
            body { 
                background: white !important; 
                margin: 0 !important;
                padding: 0 !important;
            }
            nav, footer, .print-hide { display: none !important; }
            .print-container { 
                box-shadow: none !important; 
                border: none !important; 
                margin: 0 !important; 
                padding: 20px !important;
                width: 100% !important;
            }
        }
    </style>

    <div class="py-6 print:py-0">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 print:px-0 print:max-w-none">
            
            <div class="mb-8 flex justify-between items-center print:hidden">
                <div>
                    <h1 class="text-2xl font-black text-gray-800">Batch Notice of Delinquency</h1>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">{{ $barangay->brgy_name }} Collection Area</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="bg-amber-600 hover:bg-amber-700 text-white px-8 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg transition-all">
                        <i class="fas fa-print"></i> Print All notices ({{ count($delinquentData) }})
                    </button>
                    <a href="{{ route('treasury.gis.index') }}" class="bg-white border text-gray-600 px-6 py-3 rounded-xl font-bold shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Return to GIS
                    </a>
                </div>
            </div>

            @include('modules.treasury.rpt_payments.partials.nod_content', [
                'delinquentData' => $delinquentData,
                'generatedBy' => Auth::user()->name
            ])

        </div>
    </div>
</x-admin.app>
