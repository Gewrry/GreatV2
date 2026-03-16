<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')
            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Classifications & Actual Uses --}}
                <div class="bg-white rounded-xl shadow">
                    <div class="px-5 py-3 border-b font-bold text-gray-700"><i class="fas fa-tags text-blue-500 mr-1"></i> Property Classifications</div>
                    <div class="p-5 space-y-2 max-h-60 overflow-y-auto">
                        @foreach($classes as $cls)
                            <details class="border rounded-lg">
                                <summary class="px-3 py-2 cursor-pointer font-medium text-sm text-gray-700 flex items-center justify-between">
                                    <span>{{ $cls->code }} — {{ $cls->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $cls->actualUses->count() }} actual uses</span>
                                </summary>
                                <ul class="px-4 pb-2 text-xs text-gray-500 space-y-1 mt-2">
                                    @foreach($cls->actualUses as $au)
                                        <li class="flex items-center justify-between">
                                            <span>{{ $au->code }} — {{ $au->name }}</span>
                                            <span class="{{ $au->is_active ? 'text-green-500' : 'text-gray-400' }}">{{ $au->is_active ? 'Active' : 'Inactive' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @endforeach
                    </div>
                    <div class="px-5 py-4 border-t bg-gray-50">
                        <form action="{{ route('rpt.settings.classes.store') }}" method="POST" class="flex gap-2 mb-2">
                            @csrf
                            <input type="text" name="code" placeholder="Code (e.g. RES)" maxlength="10" required class="border rounded px-2 py-1 text-xs flex-1">
                            <input type="text" name="name" placeholder="Classification Name" required class="border rounded px-2 py-1 text-xs flex-1">
                            <button type="submit" class="bg-blue-600 text-white text-xs px-3 py-1 rounded">Add</button>
                        </form>
                        <form action="{{ route('rpt.settings.actual-uses.store') }}" method="POST" class="flex gap-2">
                            @csrf
                            <select name="rpta_class_id" required class="border rounded px-2 py-1 text-xs">
                                <option value="">Class</option>
                                @foreach($classes as $cls) <option value="{{ $cls->id }}">{{ $cls->name }}</option> @endforeach
                            </select>
                            <input type="text" name="code" placeholder="Code" maxlength="20" required class="border rounded px-2 py-1 text-xs w-24">
                            <input type="text" name="name" placeholder="Actual Use Name" required class="border rounded px-2 py-1 text-xs flex-1">
                            <button type="submit" class="bg-green-600 text-white text-xs px-3 py-1 rounded">Add Use</button>
                        </form>
                    </div>
                </div>

                {{-- Building Types --}}
                <div class="bg-white rounded-xl shadow">
                    <div class="px-5 py-3 border-b font-bold text-gray-700"><i class="fas fa-building text-yellow-500 mr-1"></i> Building Types</div>
                    <div class="p-5 overflow-x-auto max-h-60 overflow-y-auto">
                        <table class="w-full text-xs">
                            <thead class="text-gray-500 uppercase bg-gray-50">
                                <tr><th class="px-2 py-1 text-left">Code</th><th class="px-2 py-1 text-left">Name</th><th class="px-2 py-1 text-right">Cost/sqm</th><th class="px-2 py-1 text-right">Life (yrs)</th></tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($bldgTypes as $bt)
                                    <tr><td class="px-2 py-1 font-mono">{{ $bt->code }}</td><td class="px-2 py-1">{{ $bt->name }}</td><td class="px-2 py-1 text-right">{{ number_format($bt->base_construction_cost, 2) }}</td><td class="px-2 py-1 text-right">{{ $bt->useful_life }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t bg-gray-50">
                        <form action="{{ route('rpt.settings.bldg-types.store') }}" method="POST" class="grid grid-cols-2 gap-2">
                            @csrf
                            <input type="text" name="code" placeholder="Code" maxlength="20" required class="border rounded px-2 py-1 text-xs">
                            <input type="text" name="name" placeholder="Name" required class="border rounded px-2 py-1 text-xs">
                            <input type="number" name="base_construction_cost" placeholder="Cost/sqm" step="0.01" min="0" required class="border rounded px-2 py-1 text-xs">
                            <input type="number" name="useful_life" placeholder="Useful Life (yrs)" min="1" required class="border rounded px-2 py-1 text-xs">
                            <input type="number" name="residual_value_rate" placeholder="Residual Rate (e.g. 0.2)" step="0.0001" min="0" max="1" required class="border rounded px-2 py-1 text-xs">
                            <button type="submit" class="bg-yellow-500 text-white text-xs px-3 py-1 rounded">Add Building Type</button>
                        </form>
                    </div>
                </div>

                {{-- Revision Years --}}
                <div class="bg-white rounded-xl shadow">
                    <div class="px-5 py-3 border-b font-bold text-gray-700"><i class="fas fa-calendar-alt text-purple-500 mr-1"></i> General Revision Years</div>
                    <div class="p-5 space-y-2">
                        @foreach($revisionYears as $ry)
                            <div class="flex items-center justify-between border rounded-lg px-3 py-2">
                                <span class="text-sm font-medium">{{ $ry->year }}</span>
                                @if($ry->is_current)
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Current</span>
                                @else
                                    <form action="{{ route('rpt.settings.revision-years.set-current', $ry) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-500 hover:underline">Set Current</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                        <form action="{{ route('rpt.settings.revision-years.store') }}" method="POST" class="flex gap-2 mt-3">
                            @csrf
                            <input type="number" name="year" placeholder="{{ date('Y') }}" min="2000" required class="border rounded px-2 py-1 text-xs flex-1">
                            <label class="flex items-center gap-1 text-xs"><input type="checkbox" name="is_current" value="1"> Set as Current</label>
                            <button type="submit" class="bg-purple-600 text-white text-xs px-3 py-1 rounded">Add Year</button>
                        </form>
                    </div>
                </div>

                {{-- Signatories --}}
                <div class="bg-white rounded-xl shadow">
                    <div class="px-5 py-3 border-b font-bold text-gray-700"><i class="fas fa-signature text-red-500 mr-1"></i> Signatories</div>
                    <div class="p-5 space-y-2 max-h-48 overflow-y-auto">
                        @foreach($signatories as $sig)
                            <div class="border rounded-lg px-3 py-2 text-sm">
                                <div class="font-medium text-gray-800">{{ $sig->name }}</div>
                                <div class="text-xs text-gray-500">{{ $sig->role }} — {{ $sig->designation }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-5 py-4 border-t bg-gray-50">
                        <form action="{{ route('rpt.settings.signatories.store') }}" method="POST" class="grid grid-cols-2 gap-2">
                            @csrf
                            <input type="text" name="role" placeholder="Role (e.g. Assessor)" required class="border rounded px-2 py-1 text-xs">
                            <input type="text" name="name" placeholder="Full Name" required class="border rounded px-2 py-1 text-xs">
                            <input type="text" name="designation" placeholder="Designation (optional)" class="border rounded px-2 py-1 text-xs">
                            <button type="submit" class="bg-red-600 text-white text-xs px-3 py-1 rounded">Add Signatory</button>
                        </form>
                    </div>
                </div>

                {{-- ARP Configuration --}}
                <div class="bg-white rounded-xl shadow">
                    <div class="px-5 py-3 border-b font-bold text-gray-700"><i class="fas fa-id-card text-blue-600 mr-1"></i> ARP Configuration</div>
                    <div class="p-5">
                        <form action="{{ route('rpt.settings.global.update') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">ARP Format Notice</label>
                                    <p class="text-[10px] text-gray-400 italic">Format is fixed to: [District]-[Barangay]-[Sequence]</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Sequence Padding (Digits)</label>
                                    <input type="number" name="arp_sequence_padding" value="{{ $settings['arp_sequence_padding'] ?? 5 }}" min="3" max="10" required class="w-full border rounded px-3 py-2 text-sm">
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white text-xs py-2 rounded-lg font-bold">Save Configuration</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tax Amnesty Configuration --}}
                <div class="bg-white rounded-xl shadow lg:col-span-2">
                    <div class="px-5 py-3 border-b border-rose-100 font-bold text-rose-700 bg-rose-50/50 rounded-t-xl"><i class="fas fa-gift text-rose-500 mr-1"></i> Tax Amnesty (Penalty Waiver)</div>
                    <div class="p-5">
                        <form action="{{ route('rpt.settings.global.update') }}" method="POST">
                            @csrf
                            <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                                <div class="flex-1 w-full">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Amnesty Start Date</label>
                                    <input type="date" name="amnesty_start_date" value="{{ $settings['amnesty_start_date'] ?? '' }}" class="w-full border rounded px-3 py-2 text-sm focus:ring-rose-500 focus:border-rose-500">
                                </div>
                                <div class="flex-1 w-full">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Amnesty End Date</label>
                                    <input type="date" name="amnesty_end_date" value="{{ $settings['amnesty_end_date'] ?? '' }}" class="w-full border rounded px-3 py-2 text-sm focus:ring-rose-500 focus:border-rose-500">
                                </div>
                                <button type="submit" class="bg-rose-600 text-white text-xs px-6 py-2.5 rounded-lg font-bold w-full md:w-auto hover:bg-rose-700 transition shadow-sm">Enable/Update Amnesty</button>
                                <button type="button" 
                                    onclick="document.querySelector('input[name=amnesty_start_date]').value=''; document.querySelector('input[name=amnesty_end_date]').value=''; this.previousElementSibling.click();" 
                                    class="bg-gray-100 text-gray-600 text-xs px-4 py-2.5 rounded-lg font-bold w-full md:w-auto hover:bg-gray-200 transition">Clear</button>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 italic">When active, all accumulated RPT penalties will be fully waived (computed as 0) if payment is made between the Start and End dates.</p>
                        </form>
                    </div>
                </div>

                {{-- Assessment Levels (Rates) --}}
                <div class="bg-white rounded-xl shadow lg:col-span-2" x-data="{ showAddLevel: false }">
                    <div class="px-5 py-3 border-b font-bold text-gray-700 flex items-center justify-between">
                        <span><i class="fas fa-percent text-indigo-500 mr-1"></i> Assessment Levels (Revision Year: {{ $currentRevision?->year ?? 'NONE' }})</span>
                        <div class="flex items-center gap-2">
                             @if($currentRevision)
                                <button @click="showAddLevel = !showAddLevel" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                    <i class="fas fa-plus-circle"></i> Add Rate
                                </button>
                             @endif
                             <span class="text-[10px] font-normal text-gray-400 hidden sm:block">Determines the Assessed Value based on Actual Use & Market Value range</span>
                        </div>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-gray-500 uppercase font-bold text-[10px]">
                                <tr>
                                    <th class="px-5 py-2">Actual Use</th>
                                    <th class="px-5 py-2">Min Value</th>
                                    <th class="px-5 py-2">Max Value</th>
                                    <th class="px-5 py-2">Rate (%)</th>
                                    <th class="px-5 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($assessmentLevels as $lvl)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-5 py-2 font-medium">{{ $lvl->actualUse?->name }}</td>
                                        <td class="px-5 py-2">₱{{ number_format($lvl->min_value, 2) }}</td>
                                        <td class="px-5 py-2">{{ $lvl->max_value ? '₱'.number_format($lvl->max_value, 2) : 'No Limit' }}</td>
                                        <td class="px-5 py-2 font-bold text-indigo-600">{{ $lvl->rate * 100 }}%</td>
                                        <td class="px-5 py-2 text-right">
                                            <form action="{{ route('rpt.settings.assessment-levels.destroy', $lvl) }}" method="POST" onsubmit="return confirm('Remove this rule?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-300 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($currentRevision)
                    <div class="px-5 py-4 border-t bg-indigo-50/50" x-show="showAddLevel" x-transition>
                        <div class="mb-3 text-xs font-bold text-indigo-600">Register New Assessment Rate</div>
                        <form action="{{ route('rpt.settings.assessment-levels.store') }}" method="POST" class="grid grid-cols-2 lg:grid-cols-5 gap-2">
                            @csrf
                            <input type="hidden" name="revision_year_id" value="{{ $currentRevision->id }}">
                            <select name="rpta_actual_use_id" required class="border rounded px-2 py-1 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">— Select Actual Use —</option>
                                @foreach($classes as $cls)
                                    <optgroup label="{{ $cls->name }}">
                                        @foreach($cls->actualUses as $au)
                                            <option value="{{ $au->id }}">{{ $au->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <input type="number" name="min_value" placeholder="Min Value" step="0.01" value="0.00" required class="border rounded px-2 py-1 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                            <input type="number" name="max_value" placeholder="Max Value (empty for none)" step="0.01" class="border rounded px-2 py-1 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                            <input type="number" name="rate" placeholder="Rate % (e.g. 10)" step="1" min="0" max="100" required class="border rounded px-2 py-1 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="submit" class="bg-indigo-600 text-white text-xs px-3 py-1 rounded font-bold shadow-sm hover:bg-indigo-700 transition">Save Rate</button>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- Schedule of Market Values (SMV) --}}
                <div class="bg-white rounded-xl shadow lg:col-span-2">
                    <div class="px-5 py-3 border-b font-bold text-gray-700 flex items-center justify-between">
                        <span><i class="fas fa-map-marked-alt text-emerald-500 mr-1"></i> Schedule of Market Values (SMV)</span>
                        <span class="text-[10px] font-normal text-gray-400">Base Unit Value per square meter (Revision Year: {{ $currentRevision?->year ?? 'NONE' }})</span>
                    </div>
                    <div class="p-0 overflow-x-auto max-h-80 overflow-y-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-gray-500 uppercase font-bold text-[10px]">
                                <tr>
                                    <th class="px-5 py-2">Barangay</th>
                                    <th class="px-5 py-2">Actual Use</th>
                                    <th class="px-5 py-2 text-right">Unit Value (₱/sqm)</th>
                                    <th class="px-5 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($unitValues as $uv)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-5 py-2 font-medium text-gray-400">{{ $uv->barangay?->brgy_name ?? 'ALL BARANGAYS' }}</td>
                                        <td class="px-5 py-2 font-medium">{{ $uv->actualUse?->name }}</td>
                                        <td class="px-5 py-2 text-right font-bold text-emerald-600">₱{{ number_format($uv->value_per_sqm, 2) }}</td>
                                        <td class="px-5 py-2 text-right">
                                            <form action="{{ route('rpt.settings.unit-values.destroy', $uv) }}" method="POST" onsubmit="return confirm('Remove this unit value?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-300 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($unitValues->isEmpty())
                                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 italic">No market values configured for this year.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if($currentRevision)
                    <div class="px-5 py-4 border-t bg-gray-50">
                        <form action="{{ route('rpt.settings.unit-values.store') }}" method="POST" class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                            @csrf
                            <input type="hidden" name="revision_year_id" value="{{ $currentRevision->id }}">
                            <select name="barangay_id" class="border rounded px-2 py-1 text-xs">
                                <option value="">— All Barangays —</option>
                                @foreach($barangays as $brgy) <option value="{{ $brgy->id }}">{{ $brgy->brgy_name }}</option> @endforeach
                            </select>
                            <select name="rpta_actual_use_id" required class="border rounded px-2 py-1 text-xs">
                                <option value="">— Actual Use —</option>
                                @foreach($classes as $cls)
                                    <optgroup label="{{ $cls->name }}">
                                        @foreach($cls->actualUses as $au)
                                            <option value="{{ $au->id }}">{{ $au->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <input type="number" name="value_per_sqm" placeholder="₱/sqm" step="0.01" required class="border rounded px-2 py-1 text-xs">
                            <button type="submit" class="bg-emerald-600 text-white text-xs px-3 py-1 rounded font-bold">Add Unit Value</button>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- Barangay Codes --}}
                <div class="bg-white rounded-xl shadow lg:col-span-2">
                    <div class="px-5 py-3 border-b font-bold text-gray-700 font-bold flex items-center justify-between">
                        <span><i class="fas fa-map-marker-alt text-green-500 mr-1"></i> Barangay District & Codes</span>
                        <span class="text-xs font-normal text-gray-400">Essential for ARP [District]-[Barangay] generation</span>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 text-left">Barangay Name</th>
                                    <th class="px-6 py-3 text-left">District Code (2 chars)</th>
                                    <th class="px-6 py-3 text-left">Barangay Code (4 chars)</th>
                                    <th class="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-gray-600">
                                @foreach($barangays as $brgy)
                                    <tr>
                                        <form action="{{ route('rpt.settings.barangay-codes.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="barangay_id" value="{{ $brgy->id }}">
                                            <td class="px-6 py-3 font-medium text-gray-900">{{ $brgy->brgy_name }}</td>
                                            <td class="px-6 py-3">
                                                <input type="text" name="brgy_district" value="{{ $brgy->brgy_district }}" placeholder="04" maxlength="10" class="border rounded px-2 py-1 text-xs w-20">
                                            </td>
                                            <td class="px-6 py-3">
                                                <input type="text" name="brgy_code" value="{{ $brgy->brgy_code }}" placeholder="0012" maxlength="10" class="border rounded px-2 py-1 text-xs w-24">
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs font-bold">Update</button>
                                            </td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
