<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bukti Pemesanan Tiket</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>
            @endif

            <section class="overflow-hidden rounded-xl border border-indigo-100 bg-white shadow-sm">
                <div class="bg-indigo-700 px-6 py-5 text-white">
                    <p class="text-sm font-medium uppercase tracking-widest text-indigo-100">Tiket Acara Kampus</p>
                    <h1 class="mt-2 text-2xl font-bold">{{ $booking->event->name }}</h1>
                </div>
                <dl class="grid gap-5 p-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-gray-500">Nama peserta</dt>
                        <dd class="mt-1 font-medium">{{ $booking->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Status pemesanan</dt>
                        <dd class="mt-1 font-medium">{{ ucfirst($booking->status) }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-gray-500">Kode tiket</dt>
                        <dd class="mt-1 break-all font-mono text-lg font-semibold text-indigo-800">{{ $booking->ticket_code }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-gray-500">Status email bukti</dt>
                        <dd class="mt-1 font-medium">
                            @if ($booking->confirmation_sent_at)
                                Terkirim pada {{ $booking->confirmation_sent_at->format('d M Y H:i') }}
                            @else
                                Sedang diproses oleh antrean email.
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            <a href="{{ route('booking.index') }}" class="inline-flex font-semibold text-indigo-700 hover:text-indigo-900">
                Kembali ke Tiket Saya
            </a>
        </div>
    </div>
</x-app-layout>