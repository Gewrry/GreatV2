{{-- resources/views/client/applications/retire.blade.php --}}
@extends('client.layouts.app')

@section('title', 'Retire Business: ' . ($application->business->business_name ?? ''))

@section('content')
    <div class="max-w-3xl mx-auto px-4 mt-8">
        {{-- Header --}}
        <div class="mb-8 p-6 bg-gradient-to-r from-red-600/90 to-red-800/90 backdrop-blur-xl rounded-[2rem] shadow-2xl shadow-red-500/20 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-red-400/20 rounded-full blur-3xl"></div>

            <a href="{{ route('client.applications.show', $application->id) }}" class="text-[10px] font-black uppercase tracking-widest text-white/70 hover:text-white transition-all mb-4 inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 rounded-xl border border-white/10 relative z-10">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Dashboard
            </a>

            <div class="relative z-10 pt-2">
                <h1 class="text-3xl font-black tracking-tightest leading-none">Request Business Retirement</h1>
                <p class="text-white/80 text-xs font-bold mt-2 uppercase tracking-wide">
                    {{ $application->application_number }} &middot; {{ $application->business->business_name ?? '—' }}
                </p>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm text-red-600 font-bold shadow-sm">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                {{ session('error') }}
            </div>
        @endif

        {{-- Payment Summary Panel --}}
        <div class="mb-8 bg-white rounded-[2rem] border border-lumot/20 shadow-xl shadow-black/5 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-white shadow-sm border border-gray-100 flex items-center justify-center text-xs">📊</span>
                <h2 class="text-[10px] font-black text-gray/60 uppercase tracking-widest">Financial Clearance Check</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-1">Total Assessed Fees</p>
                        <p class="text-lg font-black text-gray/70">₱{{ number_format($totalAssessed, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-1">Total Paid</p>
                        <p class="text-lg font-black text-green">₱{{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <div class="px-4 py-2 {{ $canRetire ? 'bg-green/5 border border-green/20' : 'bg-red-50 border border-red-200' }} rounded-xl">
                        <p class="text-[9px] font-black {{ $canRetire ? 'text-green/60' : 'text-red-500/70' }} uppercase tracking-widest mb-1">Outstanding Balance</p>
                        <p class="text-xl font-black {{ $canRetire ? 'text-green' : 'text-red-600' }}">₱{{ number_format($outstandingBalance, 2) }}</p>
                    </div>
                </div>

                @if(!$canRetire)
                    <div class="mt-6 p-4 bg-red-500/5 border border-red-500/20 rounded-2xl flex items-start gap-4 shadow-inner">
                        <div class="w-10 h-10 bg-red-500/10 rounded-xl flex items-center justify-center text-lg shrink-0 border border-red-500/10">⛔</div>
                        <div>
                            <p class="text-sm font-black text-red-700 uppercase tracking-widest leading-tight mb-1">Retirement Blocked</p>
                            <p class="text-xs text-red-600/80 font-bold leading-relaxed">
                                You cannot retire this business because there is an outstanding balance of <strong>₱{{ number_format($outstandingBalance, 2) }}</strong>. 
                                Please click "Pay Balance" to settle your remaining installments, then return to this page to complete your retirement request.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('client.payment.show', $application->id) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-red-600/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                    Pay Balance
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Retirement Form --}}
        @if($canRetire)
            <div class="bg-white rounded-[2rem] border border-lumot/20 shadow-xl shadow-black/5 overflow-hidden">
                <form action="{{ route('client.applications.retire', $application->id) }}" method="POST">
                    @csrf
                    <div class="px-6 py-4 bg-red-50/50 border-b border-red-100 flex items-center gap-2">
                        <span class="text-xs">📝</span>
                        <h2 class="text-[10px] font-black text-red-700 uppercase tracking-widest">Retirement Declaration</h2>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="p-4 bg-yellow/5 border border-yellow/20 rounded-2xl">
                            <p class="text-xs text-yellow-800 font-medium leading-relaxed">
                                <strong class="text-yellow-900 font-bold block mb-1">Important Note:</strong>
                                Submitting this form will send a retirement request to the Licensing Office. 
                                You remain liable for any undisclosed obligations. Your business permit will be marked as inactive upon confirmation.
                            </p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray/60 uppercase tracking-widest mb-1.5 ml-1">Reason for Retirement <span class="text-red-500">*</span></label>
                            <select name="retirement_reason" required
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block p-3 px-4 font-semibold shadow-sm transition-all">
                                <option value="">Select a reason...</option>
                                <option value="closure" {{ old('retirement_reason') == 'closure' ? 'selected' : '' }}>Permanent Closure / Cessation of Operations</option>
                                <option value="bankruptcy" {{ old('retirement_reason') == 'bankruptcy' ? 'selected' : '' }}>Bankruptcy</option>
                                <option value="transfer" {{ old('retirement_reason') == 'transfer' ? 'selected' : '' }}>Transfer of Location (Outside Municipality)</option>
                                <option value="owner_death" {{ old('retirement_reason') == 'owner_death' ? 'selected' : '' }}>Death of Owner</option>
                                <option value="change_ownership" {{ old('retirement_reason') == 'change_ownership' ? 'selected' : '' }}>Change of Ownership / Sale of Business</option>
                                <option value="other" {{ old('retirement_reason') == 'other' ? 'selected' : '' }}>Other (Specify in remarks)</option>
                            </select>
                            @error('retirement_reason')<p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray/60 uppercase tracking-widest mb-1.5 ml-1">Effective Date of Retirement <span class="text-red-500">*</span></label>
                            <input type="date" name="retirement_date" required value="{{ old('retirement_date', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block p-3 px-4 font-semibold shadow-sm transition-all">
                            @error('retirement_date')<p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray/60 uppercase tracking-widest mb-1.5 ml-1">Additional Remarks</label>
                            <textarea name="retirement_remarks" rows="3" placeholder="Provide any additional details or explanations here..."
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block p-3 px-4 font-semibold shadow-sm transition-all resize-none">{{ old('retirement_remarks') }}</textarea>
                            @error('retirement_remarks')<p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <div class="px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl flex items-start gap-3">
                            <input type="checkbox" id="confirm_declaration" required
                                class="mt-1 w-4 h-4 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500">
                            <label for="confirm_declaration" class="text-xs text-gray-600 leading-relaxed font-bold cursor-pointer">
                                I hereby declare under the penalties of perjury that the foregoing information is true and correct to the best of my knowledge and belief.
                            </label>
                        </div>
                    </div>

                    <div class="px-6 py-5 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('client.applications.show', $application->id) }}" class="px-6 py-3 text-gray/60 hover:text-gray text-[11px] font-black uppercase tracking-widest transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-red-600/20 active:translate-y-0.5" onclick="return confirm('Submit this business retirement request? This action will notify the licensing office.')">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
@endsection
