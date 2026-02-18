<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">User Activity Audit</h1>
                    <p class="text-gray-500 mt-2 font-medium">Performance and workload summary of assessment revisions by authorized personnel.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                    <a href="{{ route('rpt.reports.user_activity_audit.export.pdf') }}" class="px-5 py-2.5 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($stats as $user)
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-500 group relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-purple-600/5 rounded-full group-hover:bg-purple-600/10 transition-colors duration-500 pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-5 mb-8">
                            <div class="w-16 h-16 rounded-2xl bg-gray-900 flex items-center justify-center text-white shadow-xl shadow-gray-200 group-hover:scale-110 transition-transform duration-500">
                                <span class="text-xl font-black uppercase">{{ substr($user->encoded_by, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 group-hover:text-purple-600 transition-colors">{{ $user->encoded_by }}</h3>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] font-mono">Encoder Profile</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100/50">
                                <span class="block text-2xl font-black text-gray-900">{{ number_format($user->total_revisions) }}</span>
                                <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Revisions</span>
                            </div>
                            <div class="bg-purple-50 rounded-2xl p-4 text-center border border-purple-100/50">
                                <span class="block text-sm font-black text-purple-600">Active</span>
                                <span class="block text-[9px] font-black text-purple-400 uppercase tracking-widest mt-1">Status</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-black text-gray-400 uppercase tracking-widest">Last Activity</span>
                                <span class="font-black text-gray-900">{{ $user->last_activity->diffForHumans() }}</span>
                            </div>
                            <p class="text-[10px] font-bold text-gray-400 mt-2 text-right italic">{{ $user->last_activity->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full bg-white rounded-[2rem] p-24 text-center border-2 border-dashed border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800">No user activity recorded</h3>
                    <p class="text-gray-400 mt-2 font-medium">Revisions logged by appraisers and encoders will appear here.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.app>
