@extends('client.layouts.app')

@section('title', 'E-Receipt — OR #' . $payment->or_no)

@section('content')
<div class="max-w-xl mx-auto px-4 py-8 pb-28 sm:pb-8">

    {{-- Back Link --}}
    <a href="{{ route('client.rpt-pay.soa', $payment->billing->tax_declaration_id) }}"
        class="inline-flex items-center gap-2 text-sm text-teal-600 hover:text-teal-800 font-medium mb-6 transition">
        <i class="fas fa-arrow-left"></i> Back to Account
    </a>

    {{-- Receipt Card --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden" id="receipt-card">

        {{-- Header --}}
        <div class="px-6 py-5 text-white text-center" style="background:linear-gradient(135deg,#0d9488,#059669);">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-3">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <h1 class="text-xl font-extrabold tracking-tight">Official E-Receipt</h1>
            <p class="text-white/70 text-xs mt-1 uppercase tracking-widest">Real Property Tax Payment</p>
        </div>

        {{-- Status Badge --}}
        <div class="flex justify-center -mt-4 mb-1 z-10 relative">
            <span class="bg-emerald-500 text-white text-xs font-black uppercase tracking-widest px-4 py-1.5 rounded-full shadow-lg">
                <i class="fas fa-check-circle mr-1"></i> Payment Confirmed
            </span>
        </div>

        <div class="px-6 py-5 space-y-4">

            {{-- Reference Block --}}
            <div class="bg-gray-50 rounded-xl p-4 text-center border border-dashed border-gray-200">
                <div class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">OR / Reference No.</div>
                <div class="text-2xl font-extrabold text-gray-900 tracking-wider font-mono">{{ $payment->or_no }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $payment->payment_date->format('F d, Y') }}</div>
            </div>

            {{-- Property Details --}}
            @php
                $td   = $payment->billing->taxDeclaration;
                $prop = $td->property;
            @endphp
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Property Owner</span>
                    <span class="font-bold text-gray-800 text-right max-w-[55%]">{{ $prop->owner_name ?? '—' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">TD No.</span>
                    <span class="font-semibold text-gray-700">{{ $td->td_no }}</span>
                </div>
                @if($prop->arp_no)
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">ARP / PIN</span>
                    <span class="font-semibold text-gray-700">{{ $prop->arp_no }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Barangay</span>
                    <span class="font-semibold text-gray-700">{{ $prop->barangay?->brgy_name ?? '—' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Period Covered</span>
                    <span class="font-semibold text-gray-700">{{ $payment->billing->tax_year }} — Q{{ $payment->billing->quarter }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Payment Mode</span>
                    <span class="font-semibold text-gray-700 capitalize">{{ str_replace('online_', '', $payment->payment_mode) }}</span>
                </div>
            </div>

            {{-- Amount Breakdown --}}
            <div class="bg-teal-50/60 rounded-xl p-4 border border-teal-100 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Basic Tax</span>
                    <span>₱{{ number_format($payment->basic_tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>SEF</span>
                    <span>₱{{ number_format($payment->sef_tax, 2) }}</span>
                </div>
                @if($payment->penalty > 0)
                <div class="flex justify-between text-red-600">
                    <span>Penalty</span>
                    <span>₱{{ number_format($payment->penalty, 2) }}</span>
                </div>
                @endif
                @if($payment->discount > 0)
                <div class="flex justify-between text-emerald-600">
                    <span>Discount</span>
                    <span>(₱{{ number_format($payment->discount, 2) }})</span>
                </div>
                @endif
                <div class="border-t border-teal-200 pt-2 flex justify-between font-extrabold text-base text-teal-800">
                    <span>Total Paid</span>
                    <span>₱{{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>

            {{-- Legal Note --}}
            <p class="text-[10px] text-gray-400 text-center leading-relaxed">
                This electronic receipt serves as official proof of payment for Real Property Tax.
                Processed online via PayMongo. For concerns, contact the Municipal Treasury Office.
            </p>
        </div>

        {{-- Print Footer --}}
        <div class="px-6 pb-6 flex gap-3">
            <button onclick="window.print()"
                class="flex-1 py-3 rounded-xl text-white text-sm font-bold shadow-md transition-all hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2"
                style="background:linear-gradient(135deg,#0d9488,#059669);">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="{{ route('client.rpt-pay.soa', $payment->billing->tax_declaration_id) }}"
                class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition text-center flex items-center justify-center gap-2">
                <i class="fas fa-file-invoice-dollar"></i> Back to Ledger
            </a>
        </div>
    </div>

</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #receipt-card, #receipt-card * { visibility: visible; }
        #receipt-card { position: absolute; top: 0; left: 0; width: 100%; box-shadow: none; border: none; }
        nav, header, footer, a[href] { display: none !important; }
    }
</style>
@endsection
