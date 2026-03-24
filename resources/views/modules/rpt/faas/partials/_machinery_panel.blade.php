{{-- ═══════════ MACHINERY COMPONENTS ═══════════ --}}
@if(in_array($faas->property_type, ['machinery', 'mixed']))
<div id="panel-machinery" class="bg-white rounded-xl shadow border border-purple-100 overflow-hidden
    {{ session('open_tab') === 'machinery' ? 'ring-2 ring-purple-400' : '' }}">

    <div class="px-6 py-3 bg-purple-50 border-b flex items-center justify-between">
        <h3 class="font-bold text-purple-800 text-sm flex items-center gap-2">
            <i class="fas fa-cogs text-purple-500"></i> Machineries / Equipment
            <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $faas->machineries->count() }} added</span>
        </h3>
        @if($faas->isEditable() && strtoupper(trim($faas->revision_type)) !== 'TRANSFER')
            <button onclick="toggleForm('machinery-form')" class="text-xs font-semibold text-purple-700 border border-purple-200 rounded-lg px-3 py-1 hover:bg-purple-100 transition">
                <i class="fas fa-plus mr-1"></i> Add Machinery
            </button>
        @endif
    </div>

    @if($faas->machineries->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-400 border-b">
                <tr>
                    <th class="px-5 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-right">Orig. Cost</th>
                    <th class="px-4 py-2 text-right">Useful Life</th>
                    <th class="px-4 py-2 text-right">Market Value</th>
                    <th class="px-4 py-2 text-right font-bold text-purple-600">Assessed Value</th>
                    @if($faas->isEditable()) <th class="px-4 py-2"></th> @endif
                    @if($faas->isApproved()) <th class="px-4 py-2 text-right">TD Status</th> @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($faas->machineries as $mach)
                <tr class="hover:bg-purple-50/30 transition-colors">
                    <td class="px-5 py-3">
                        <div class="font-semibold text-gray-700 text-xs">{{ $mach->machine_name }}</div>
                        <div class="text-[10px] text-gray-400">{{ $mach->brand ?: '—' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600 tabular-nums">₱{{ number_format($mach->original_cost, 2) }}</td>
                    <td class="px-4 py-3 text-right text-gray-600 tabular-nums">{{ $mach->useful_life }} yrs</td>
                    <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($mach->market_value, 2) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-purple-700 tabular-nums">{{ number_format($mach->assessed_value, 2) }}</td>
                    @if($faas->isEditable())
                    <td class="px-4 py-3 text-right flex justify-end gap-2">
                        @if(strtoupper(trim($faas->revision_type)) !== 'TRANSFER')
                            <button onclick="openEditMachModal({{ $mach->id }}, {{ $mach->rpta_actual_use_id }}, '{{ addslashes($mach->machine_name) }}', {{ $mach->original_cost }}, {{ $mach->useful_life }}, {{ $mach->year_acquired }}, {{ $mach->assessment_level }})" 
                                    class="text-purple-600 hover:text-purple-800 text-xs transition">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('rpt.faas.machinery.destroy', [$faas, $mach]) }}" method="POST" onsubmit="return confirm('Remove this machinery?')">
                                @csrf @method('DELETE')
                                <button class="text-red-300 hover:text-red-500 text-xs transition"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        @endif
                    </td>
                    @endif
                    @if($faas->isApproved())
                    @php $machHasTd = $mach->taxDeclaration()->whereNotIn('status',['cancelled'])->exists(); @endphp
                    <td class="px-4 py-3 text-right">
                        @if($machHasTd)
                            @php $machTd = $mach->taxDeclaration()->whereNotIn('status',['cancelled'])->first(); @endphp
                            <div class="flex items-center justify-end gap-2">
                                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">TD Issued</span>
                                @if($machTd)
                                    <a href="{{ route('rpt.td.show', $machTd) }}" class="text-blue-500 hover:text-blue-700" title="View Tax Declaration">
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('rpt.td.create', ['faas_property_id' => $faas->id, 'component_type' => 'machinery', 'component_id' => $mach->id]) }}"
                               class="text-[10px] bg-blue-600 text-white px-2 py-1 rounded font-bold hover:bg-blue-700 whitespace-nowrap">
                                <i class="fas fa-stamp mr-0.5"></i> Generate TD
                            </a>
                        @endif
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($faas->isEditable())
    <div id="machinery-form" class="{{ session('open_tab') === 'machinery' ? '' : 'hidden' }} border-t border-dashed border-purple-200 bg-purple-50/20 machinery-calc-container">
        <form action="{{ route('rpt.faas.machinery.store', $faas) }}" method="POST" class="p-5">
            @csrf
            <h4 class="text-xs font-bold text-purple-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add Machinery / Equipment
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Actual Use <span class="text-red-400">*</span></label>
                    <select name="rpta_actual_use_id" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-300">
                        <option value="">— Select —</option>
                        @foreach($actualUses as $use)
                            <option value="{{ $use->id }}">{{ $use->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Machine Name <span class="text-red-400">*</span></label>
                    <input type="text" name="machine_name" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300" placeholder="e.g. Industrial Generator">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Original Cost (₱) <span class="text-red-400">*</span></label>
                    <input type="number" name="original_cost" step="0.01" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300" placeholder="e.g. 250000.00">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Useful Life (years) <span class="text-red-400">*</span></label>
                    <input type="number" name="useful_life" min="1" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300" placeholder="e.g. 10">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Assessment Level (0–1) <span class="text-red-400">*</span></label>
                    <input type="number" name="assessment_level" step="0.01" min="0" max="1" value="{{ old('assessment_level', '0.80') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Year Acquired <span class="text-red-400">*</span></label>
                    <input type="number" name="year_acquired" min="1900" max="{{ date('Y') }}" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="{{ date('Y') }}">
                </div>
                {{-- Depreciation is auto-calculated — shown as read-only --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Auto-Depreciation Rate</label>
                    <div class="w-full border border-dashed rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 tabular-nums dep-rate-display">—</div>
                    <p class="text-[9px] text-gray-400 mt-0.5">Age÷Life, max 80%, min 20% (MRPAAO)</p>
                </div>
            </div>

            {{-- Live Preview Box —— Auto-Depreciation Edition --}}
            <div class="mt-4 p-4 rounded-xl bg-purple-600/5 border border-purple-100/50">
                <div class="text-[9px] font-bold text-purple-600/60 uppercase tracking-widest mb-3">Live Valuation Preview</div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-[9px] text-gray-400 uppercase font-bold mb-0.5">Original Cost</div>
                        <div class="text-sm font-bold text-gray-700 tabular-nums">₱ <span class="cost-preview">0.00</span></div>
                    </div>
                    <div>
                        <div class="text-[9px] text-gray-400 uppercase font-bold mb-0.5">Depreciation Amount</div>
                        <div class="text-sm font-bold text-red-500 tabular-nums">−₱ <span class="dep-amt-preview">0.00</span></div>
                    </div>
                    <div>
                        <div class="text-[9px] text-gray-400 uppercase font-bold mb-0.5">Market Value</div>
                        <div class="text-lg font-black text-purple-900 tabular-nums">₱ <span class="mv-preview">0.00</span></div>
                    </div>
                    <div>
                        <div class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Assessed Value</div>
                        <div class="text-lg font-black text-purple-600 tabular-nums">₱ <span class="av-preview">0.00</span></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm transition">
                    <i class="fas fa-check mr-1"></i> Save Machinery & Compute
                </button>
            </div>
        </form>
    </div>
    @elseif($faas->machineries->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-xs italic">No machineries recorded.</div>
    @endif
</div>
@endif
