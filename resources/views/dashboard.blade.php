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
                    <h3 class="text-lg font-medium">Selamat datang, {{ Auth::user()->name }}.</h3>
                    <p class="mt-2 text-gray-600">
                        Pesan tiket acara kampus dan lihat bukti pemesanan Anda.
                    </p>
                    <a href="{{ route('booking.create') }}" class="mt-4 inline-flex rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                        Pesan tiket
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
