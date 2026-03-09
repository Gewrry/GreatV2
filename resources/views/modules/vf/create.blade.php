{{-- resources/views/modules/vf/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('vf.index') }}"
                class="p-2 bg-gray/10 rounded-xl hover:bg-logo-teal/10 hover:text-logo-teal transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-logo-teal/10 rounded-xl">
                    <svg class="w-6 h-6 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 4h2l2.5 8h9L19 7H7M3 4H1m2 0l1 3" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-green">Add Vehicle Franchise</h2>
                    <p class="text-xs text-gray">Vehicle Franchising · New Registration</p>
                </div>
            </div>
        </div>
    </x-slot>

    <form action="{{ route('vf.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- ===================== PERMIT DETAILS ===================== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray/10 bg-logo-teal/5">
                <svg class="w-4 h-4 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-sm font-bold text-green uppercase tracking-wide">Permit Details</h3>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Permit Type --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Permit Type <span class="text-red-400">*</span>
                    </label>
                    <select name="permit_type" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select Type —</option>
                        <option value="new" @selected(old('permit_type') == 'new')>New</option>
                        <option value="renewal" @selected(old('permit_type') == 'renewal')>Renewal</option>
                        <option value="transfer" @selected(old('permit_type') == 'transfer')>Transfer</option>
                        <option value="amendment" @selected(old('permit_type') == 'amendment')>Amendment</option>
                    </select>
                    @error('permit_type')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Permit Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Permit Number <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="permit_number" value="{{ old('permit_number', $nextPermitNumber) }}"
                        placeholder="e.g. 589-2026" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                    @error('permit_number')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Permit Date --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Permit Date <span class="text-red-400">*</span>
                    </label>
                    <input type="date" name="permit_date" value="{{ old('permit_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all" />
                    @error('permit_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Franchise Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Franchise Number</label>
                    <input type="number" name="fn_number" value="{{ old('fn_number', $nextFnNumber) }}"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green font-bold bg-logo-teal/5 transition-all"
                        readonly />
                    @error('fn_number')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TODA --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Select TODA <span class="text-red-400">*</span>
                    </label>
                    <select name="toda_id" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select TODA —</option>
                        @foreach ($todas ?? [] as $toda)
                            <option value="{{ $toda->id }}" @selected(old('toda_id') == $toda->id)>{{ $toda->toda_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('toda_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- License Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">License Number</label>
                    <input type="text" name="license_number" value="{{ old('license_number') }}"
                        placeholder="Driver's license number"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>

                {{-- Driver Contact --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Driver Contact # <span
                            class="text-gray/40 font-normal">(optional)</span></label>
                    <input type="text" name="driver_contact" value="{{ old('driver_contact') }}"
                        placeholder="e.g. 09XX XXX XXXX"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>

                {{-- Driver Name --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Driver Name <span
                            class="text-gray/40 font-normal">(optional)</span></label>
                    <input type="text" name="driver_name" value="{{ old('driver_name') }}"
                        placeholder="Driver full name"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>

                {{-- Remarks --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Remarks <span
                            class="text-gray/40 font-normal">(optional)</span></label>
                    <textarea name="remarks" rows="1" placeholder="e.g. NEW - PRIVATE WITH DEED OF SALE"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all resize-none">{{ old('remarks') }}</textarea>
                </div>

            </div>
        </div>

        {{-- ===================== APPLICANT INFORMATION ===================== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray/10 bg-logo-blue/5">
                <svg class="w-4 h-4 text-logo-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h3 class="text-sm font-bold text-green uppercase tracking-wide">Applicant Information</h3>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Last Name --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Last Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                        placeholder="Enter Last Name" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                    @error('last_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- First Name --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        First Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                        placeholder="Enter First Name" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                    @error('first_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Middle Name --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Middle Name <span
                            class="text-gray/40 font-normal">(optional)</span></label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                        placeholder="Enter Middle Name"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>

                {{-- Citizenship --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Citizenship <span
                            class="text-gray/40 font-normal">(optional)</span></label>
                    <input type="text" name="citizenship" value="{{ old('citizenship', 'FILIPINO') }}"
                        placeholder="Citizenship"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>

                {{-- Civil Status --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Civil Status <span class="text-red-400">*</span>
                    </label>
                    <select name="civil_status" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select —</option>
                        <option value="single" @selected(old('civil_status') == 'single')>Single</option>
                        <option value="married" @selected(old('civil_status') == 'married')>Married</option>
                        <option value="widowed" @selected(old('civil_status') == 'widowed')>Widowed</option>
                        <option value="separated" @selected(old('civil_status') == 'separated')>Separated</option>
                    </select>
                    @error('civil_status')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Gender <span class="text-red-400">*</span>
                    </label>
                    <select name="gender" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select —</option>
                        <option value="male" @selected(old('gender') == 'male')>Male</option>
                        <option value="female" @selected(old('gender') == 'female')>Female</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type of Ownership --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Type of Ownership <span
                            class="text-gray/40 font-normal">(optional)</span></label>
                    <select name="ownership_type"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select —</option>
                        <option value="private" @selected(old('ownership_type') == 'private')>Private</option>
                        <option value="for_hire" @selected(old('ownership_type') == 'for_hire')>For Hire</option>
                        <option value="government" @selected(old('ownership_type') == 'government')>Government</option>
                    </select>
                </div>

                {{-- Contact Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Contact Number <span
                            class="text-gray/40 font-normal">(optional)</span></label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                        placeholder="e.g. 09XX XXX XXXX"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>

                {{-- Birthday --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Birthday <span class="text-red-400">*</span>
                    </label>
                    <input type="date" name="birthday" value="{{ old('birthday') }}" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all" />
                    @error('birthday')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Barangay --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Barangay <span class="text-red-400">*</span>
                    </label>
                    <select name="barangay" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select Barangay —</option>
                        @foreach ($barangays ?? [] as $brgy)
                            <option value="{{ $brgy }}" @selected(old('barangay') == $brgy)>{{ $brgy }}
                            </option>
                        @endforeach
                    </select>
                    @error('barangay')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Current Address --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Current Address <span class="text-red-400">*</span>
                    </label>
                    <textarea name="current_address" rows="2" placeholder="Enter full current address" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all resize-none">{{ old('current_address') }}</textarea>
                    @error('current_address')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===================== VEHICLE INFORMATION ===================== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray/10 bg-logo-green/5">
                <svg class="w-4 h-4 text-logo-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 4h2l2.5 8h9L19 7H7M3 4H1m2 0l1 3" />
                </svg>
                <h3 class="text-sm font-bold text-green uppercase tracking-wide">Vehicle Information</h3>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Make / Brand --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Make / Brand <span class="text-red-400">*</span>
                    </label>
                    <select name="make" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select Brand —</option>
                        @foreach ($vehicleMakes ?? ['Honda', 'Yamaha', 'Kawasaki', 'Suzuki', 'TVS', 'Rusi', 'Motorstar'] as $make)
                            <option value="{{ $make }}" @selected(old('make') == $make)>{{ $make }}
                            </option>
                        @endforeach
                    </select>
                    @error('make')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Model --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Model <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g. XRM 125"
                        required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                    @error('model')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Franchise Type --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">
                        Franchise Type <span class="text-red-400">*</span>
                    </label>
                    <select name="franchise_type" required
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select Type —</option>
                        @foreach ($franchiseTypes ?? ['Tricycle', 'Kuliglig', 'Motorcycle', 'E-Bike', 'Other'] as $type)
                            <option value="{{ $type }}" @selected(old('franchise_type') == $type)>{{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('franchise_type')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Motor Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Motor Number</label>
                    <input type="text" name="motor_number" value="{{ old('motor_number') }}"
                        placeholder="Motor / Engine Number"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all font-mono" />
                </div>

                {{-- Chassis Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Chassis Number</label>
                    <input type="text" name="chassis_number" value="{{ old('chassis_number') }}"
                        placeholder="Chassis / Frame Number"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all font-mono" />
                </div>

                {{-- Plate Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Plate Number</label>
                    <input type="text" name="plate_number" value="{{ old('plate_number') }}"
                        placeholder="Plate Number"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all font-mono uppercase" />
                </div>

                {{-- Year Model --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Year Model</label>
                    <select name="year_model"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all bg-white">
                        <option value="">— Select Year —</option>
                        @for ($y = now()->year; $y >= now()->year - 30; $y--)
                            <option value="{{ $y }}" @selected(old('year_model') == $y)>{{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Color</label>
                    <input type="text" name="color" value="{{ old('color') }}" placeholder="e.g. Red/Black"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>

                {{-- Sticker Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Sticker Number</label>
                    <input type="text" name="sticker_number" value="{{ old('sticker_number') }}"
                        placeholder="Sticker / Tag Number"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all font-mono" />
                </div>

            </div>
        </div>

        {{-- ===================== COMMUNITY TAX CERTIFICATE ===================== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray/10 bg-yellow/10">
                <svg class="w-4 h-4 text-brown" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm font-bold text-green uppercase tracking-wide">Community Tax Certificate Details</h3>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- CTC Receipt Number --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">CTC Receipt Number</label>
                    <input type="text" name="ctc_receipt_number" value="{{ old('ctc_receipt_number') }}"
                        placeholder="Enter CTC Receipt"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all font-mono" />
                </div>

                {{-- Date Issued --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Date Issued</label>
                    <input type="date" name="ctc_date_issued" value="{{ old('ctc_date_issued') }}"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all" />
                </div>

                {{-- Issued At --}}
                <div>
                    <label class="block text-xs font-semibold text-gray mb-1.5">Issued At</label>
                    <input type="text" name="ctc_issued_at" value="{{ old('ctc_issued_at', 'MTO-Majayjay') }}"
                        placeholder="Issuing Office"
                        class="w-full px-3 py-2.5 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>

            </div>
        </div>

        {{-- ===================== FORM ACTIONS ===================== --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('vf.index') }}"
                class="px-6 py-2.5 text-sm font-semibold text-gray bg-white border border-gray/20 rounded-xl hover:bg-gray/10 transition-all duration-200">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-8 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl shadow-lg shadow-logo-teal/30 hover:bg-green transition-all duration-200 hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Franchise
            </button>
        </div>

    </form>

</x-app-layout>
