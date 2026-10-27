<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tiket Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto space-y-4 sm:px-6 lg:px-8">
            @forelse ($bookings as $booking)
                <article class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $booking->event->name }}</h3>
                            <p class="mt-2 text-sm text-gray-600">Kode tiket: {{ $booking->ticket_code }}</p>
                            <p class="mt-1 text-sm text-gray-600">Status pemesanan: {{ ucfirst($booking->status) }}</p>
                            <p class="mt-1 text-sm text-gray-600">
                                Email bukti:
                                {{ $booking->confirmation_sent_at ? 'Sudah dikirim' : 'Sedang diproses' }}
                            </p>
                        </div>
                        <a href="{{ route('booking.show', $booking) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">
                            Lihat bukti tiket
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg bg-white p-6 text-gray-600 shadow-sm">
                    <p>Kamu belum mempunyai tiket.</p>
                    <a href="{{ route('booking.create') }}" class="mt-4 inline-block font-semibold text-indigo-700 hover:text-indigo-900">
                        Pesan tiket
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>