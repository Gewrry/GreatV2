<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Draft Tax Declaration Preview - {{ $faas->arp_no ?? 'PENDING' }}</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-4xl mx-auto bg-white p-16 shadow-2xl relative overflow-hidden">
        {{-- Watermark --}}
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03] select-none">
            <span class="text-[12rem] font-black transform -rotate-45 text-red-600">DRAFT</span>
        </div>
        
        <div class="text-center mb-10 relative z-10 border-b-2 border-gray-800 pb-6">
            <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 mb-2">Tax Declaration of Real Property</h1>
            <p class="text-lg text-gray-600 font-medium">Provincial Government of Sorsogon</p>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-10 relative z-10">
            <div>
                <div class="text-xs text-gray-400 uppercase font-black tracking-widest mb-1">Declared Owner</div>
                <div class="font-bold text-xl text-gray-800">{{ $faas->owner_name }}</div>
                @if($faas->owner_address)
                    <div class="text-sm text-gray-600 mt-1">{{ $faas->owner_address }}</div>
                @endif
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400 uppercase font-black tracking-widest mb-1">ARP Number</div>
                <div class="font-black text-2xl text-blue-700 tracking-wider bg-blue-50 inline-block px-3 py-1 border border-blue-200 rounded">
                    {{ $faas->arp_no ?? '[ TO BE GENERATED ]' }}
                </div>
            </div>
        </div>

        <div class="mb-10 relative z-10">
            <h3 class="font-bold text-lg border-b pb-2 mb-4 border-gray-200">Property Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div><span class="text-gray-500 font-medium w-32 inline-block">Property Type:</span> <span class="font-bold uppercase">{{ $faas->property_type }}</span></div>
                <div><span class="text-gray-500 font-medium w-32 inline-block">PIN:</span> <span class="font-bold">{{ $faas->pin ?? 'N/A' }}</span></div>
                <div><span class="text-gray-500 font-medium w-32 inline-block">Location:</span> <span class="font-bold">{{ $faas->barangay?->brgy_name ?? 'N/A' }}, {{ $faas->municipality_city ?? 'Sorsogon' }}</span></div>
            </div>
        </div>

        <table class="w-full border-collapse border-2 border-gray-800 mb-10 relative z-10">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-800">
                    <th class="border-r border-gray-800 p-3 text-left font-black uppercase tracking-wider text-sm">Classification</th>
                    <th class="border-r border-gray-800 p-3 text-right font-black uppercase tracking-wider text-sm">Base Market Value</th>
                    <th class="p-3 text-right font-black uppercase tracking-wider text-sm">Assessed Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border-r border-gray-800 p-4 font-bold text-gray-800">
                        {{ $faas->property_type === 'land' ? 'Land Parcels' : ($faas->property_type === 'building' ? 'Building Improvements' : 'Machineries') }}
                    </td>
                    <td class="border-r border-gray-800 p-4 text-right font-mono text-lg">₱{{ number_format($faas->total_market_value, 2) }}</td>
                    <td class="p-4 text-right font-bold text-blue-700 font-mono text-lg bg-blue-50/50">₱{{ number_format($faas->total_assessed_value, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="mt-20 grid grid-cols-2 gap-16 relative z-10">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-12">Recommending Approval:</p>
                <div class="border-b-2 border-gray-800 mb-2 text-center font-black text-lg text-gray-800 pb-1">Municipal Assessor</div>
                <p class="text-sm text-center text-gray-600">Date: {{ now()->format('F d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium mb-12 flex justify-between">
                    <span>Approved By:</span>
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded font-bold uppercase tracking-widest border border-yellow-200">Pending</span>
                </p>
                <div class="border-b-2 border-gray-800 mb-2 text-center font-black text-lg text-gray-400 pb-1 border-dashed">Provincial Assessor</div>
                <p class="text-sm text-center text-gray-400">Date: ____________________</p>
            </div>
        </div>
        
        <div class="mt-12 text-center text-xs text-gray-400 relative z-10 border-t pt-4">
            This is a system-generated preview of an unapproved FAAS. Not valid for legal purposes.
        </div>
    </div>
</body>
</html>
