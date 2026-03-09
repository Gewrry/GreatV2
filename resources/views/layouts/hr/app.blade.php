<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-logo-blue leading-tight flex items-center gap-2">
                <span class="w-1.5 h-6 bg-logo-teal rounded-full"></span>
                @yield('header_title', 'Human Resources')
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.hr.navbar')
            @yield('slot')
        </div>
    </div>
</x-app-layout>