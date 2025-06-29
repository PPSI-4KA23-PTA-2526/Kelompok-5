<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat Anda Telah Login!") }}
                </div>
            </div>
        </div>
    </div>

    <div class="md-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-3 text-gray-900">
                <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Masuk Ke Halaman Utama
            </div>
            </a>
        </div>
    </div>
</x-app-layout>