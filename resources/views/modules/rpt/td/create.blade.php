<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="bg-white rounded-xl shadow">
                {{-- Header --}}
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">New Tax Declaration</h2>
                    <a href="{{ route('rpt.td.index') }}"
                       class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                {{-- Flash / Validation errors --}}
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 border border-red-300 rounded-lg px-4 py-3 m-6">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 mx-6 mt-4 text-sm">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('rpt.td.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    {{-- Step 1: Property picker --}}
                    @include('modules.rpt.td.partials._property_selector')

                    {{-- Steps 2 & 3: Only shown once a property is chosen --}}
                    @if(isset($property))
                        {{-- Step 2: Component selector --}}
                        @include('modules.rpt.td.partials._component_list')

                        {{-- Step 3: TD field details --}}
                        @include('modules.rpt.td.partials._td_details')
                    @endif

                    {{-- Form actions --}}
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('rpt.td.index') }}"
                           class="px-4 py-2 border rounded-lg text-sm text-gray-600">Cancel</a>
                        @if(isset($property))
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                                <i class="fas fa-stamp mr-1"></i> Create Tax Declaration
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin.app>
