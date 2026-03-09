<nav class="bg-white rounded-xl shadow px-6 py-3 flex items-center gap-4 mb-4 overflow-x-auto">
    <a href="{{ route('rpt.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('rpt.index') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="{{ route('rpt.registration.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('rpt.registration.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <i class="fas fa-inbox"></i> Intakes
    </a>
    <a href="{{ route('rpt.registration.pending') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('rpt.registration.pending') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <i class="fas fa-hourglass-half"></i> Pending Appraisals
        @php $pendingAppraisals = \App\Models\RPT\RptPropertyRegistration::doesntHave('faasProperties')->where('status','registered')->count(); @endphp
        @if($pendingAppraisals > 0)
            <span class="bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5 {{ request()->routeIs('rpt.registration.pending') ? 'bg-white text-orange-500' : '' }}">{{ $pendingAppraisals }}</span>
        @endif
    </a>
    <a href="{{ route('rpt.faas.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('rpt.faas.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <i class="fas fa-file-alt"></i> FAAS Records
    </a>
    <a href="{{ route('rpt.td.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('rpt.td.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <i class="fas fa-stamp"></i> Tax Declarations
        @php $pendingForward = \App\Models\RPT\TaxDeclaration::approved()->count(); @endphp
        @if($pendingForward > 0)
            <span class="bg-red-500 text-white text-[10px] rounded-full px-1.5 py-0.5 {{ request()->routeIs('rpt.td.*') ? 'bg-white text-blue-600' : '' }} font-black">{{ $pendingForward }}</span>
        @endif
    </a>
    <a href="{{ route('rpt.online-applications.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('rpt.online-applications.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <i class="fas fa-globe"></i> Online Applications
        @php $pendingCount = \App\Models\RPT\RptOnlineApplication::where('status','pending')->count(); @endphp
        @if($pendingCount > 0)
            <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('rpt.settings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('rpt.settings.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <i class="fas fa-cog"></i> Settings
    </a>
</nav>
