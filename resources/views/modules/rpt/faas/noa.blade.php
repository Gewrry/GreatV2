<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notice of Assessment - {{ $faas->arp_no }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 p-12 font-serif">
    <div class="max-w-4xl mx-auto bg-white p-16 shadow-lg border border-gray-200">
        {{-- Header Section --}}
        <div class="text-center mb-12">
            <h2 class="text-lg font-bold uppercase tracking-widest text-gray-500">Republic of the Philippines</h2>
            <h1 class="text-2xl font-black uppercase text-gray-900">Office of the Provincial Assessor</h1>
            <p class="text-md text-gray-600">Provincial Government of Sorsogon</p>
        </div>

        <div class="flex justify-between mb-10">
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Date of Filing: {{ $faas->approved_at?->format('F d, Y') ?? now()->format('F d, Y') }}</p>
                <div class="space-y-1">
                    <p class="font-bold text-lg text-gray-800">{{ $faas->owner_name }}</p>
                    @if($faas->owner_address)
                        <p class="text-sm text-gray-600 max-w-sm">{{ $faas->owner_address }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">Notice No.</p>
                <p class="text-xl font-black text-gray-800 tracking-tighter">{{ date('Y') }}-NOA-{{ str_pad($faas->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        {{-- Subject --}}
        <h2 class="text-xl font-black uppercase text-center border-y-2 border-gray-100 py-4 mb-10 tracking-widest text-gray-800">NOTICE OF ASSESSMENT</h2>

        <div class="prose max-w-none text-gray-800 leading-relaxed mb-10">
            <p>Sir/Madam:</p>
            <p>Pursuant to the provisions of <strong>Republic Act No. 7160</strong>, otherwise known as the Local Government Code of 1991, please be informed that your real property described below has been appraised and assessed as follows:</p>
        </div>

        {{-- Assessment Table --}}
        <table class="w-full border-collapse border border-gray-800 mb-12 text-sm">
            <thead>
                <tr class="bg-gray-50 uppercase font-black text-[10px] tracking-widest text-gray-500 border-b border-gray-800">
                    <th class="border-r border-gray-800 p-3 text-left">Property Description</th>
                    <th class="border-r border-gray-800 p-3 text-left">Market Value</th>
                    <th class="border-r border-gray-800 p-3 text-left">Assessment Level</th>
                    <th class="p-3 text-left">Assessed Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                <tr>
                    <td class="border-r border-gray-800 p-4">
                        <div class="font-bold text-gray-900 uppercase">{{ $faas->property_type }}</div>
                        <div class="text-[10px] text-gray-500 mt-1 uppercase">ARP: {{ $faas->arp_no }}</div>
                        <div class="text-[10px] text-gray-500 uppercase">PIN: {{ $faas->pin ?? 'N/A' }}</div>
                        <div class="text-[10px] text-gray-500 uppercase italic mt-1">Location: {{ $faas->barangay?->brgy_name ?? 'N/A' }}</div>
                    </td>
                    <td class="border-r border-gray-800 p-4 font-mono">₱{{ number_format($faas->total_market_value, 2) }}</td>
                    <td class="border-r border-gray-800 p-4 font-mono text-center">
                        @php
                            $level = $faas->total_market_value > 0 ? ($faas->total_assessed_value / $faas->total_market_value) * 100 : 0;
                        @endphp
                        {{ number_format($level, 0) }}%
                    </td>
                    <td class="p-4 font-black text-gray-900 font-mono">₱{{ number_format($faas->total_assessed_value, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="space-y-6 text-sm text-gray-700 leading-relaxed mb-16">
            <p>The new assessment shall take effect on <strong>January 1, {{ $faas->revisionYear?->year ?? date('Y') }}</strong>.</p>
            <p>If you are not satisfied with this assessment, you may appeal the same to the <strong>Local Board of Assessment Appeals (LBAA)</strong> within sixty (60) days from the date of receipt hereof, provided that you have paid the real property tax under protest.</p>
        </div>

        <div class="mt-24 grid grid-cols-2 gap-20">
            <div class="flex flex-col items-center">
                <div class="mb-2 p-1 border border-gray-100 rounded">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=VERIFY:RPTA:{{ $faas->arp_no }}:{{ $faas->id }}" 
                         alt="Verification QR" class="w-20 h-20 opacity-80">
                </div>
                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest text-center">Scan to Verify<br>Authenticity</p>
            </div>
            <div class="text-center pt-8">
                <div class="border-b-2 border-gray-800 font-black text-lg text-gray-900 pb-1 uppercase">PROVINCIAL ASSESSOR</div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Authorized Signature</p>
            </div>
        </div>

        <div class="mt-20 pt-8 border-t border-gray-100 text-[9px] text-gray-400 uppercase tracking-[0.3em] text-center">
            This is an official legal notice produced by the RPTA System
        </div>
    </div>

    {{-- Print Script --}}
    <script>
        window.onload = function() {
            // window.print();
        }
    </script>
</body>
</html>
