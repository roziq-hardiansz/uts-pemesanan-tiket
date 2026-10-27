<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pesan Tiket Acara</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto space-y-4 sm:px-6 lg:px-8">
            @if (session('info'))
                <div class="rounded-md bg-amber-50 p-4 text-amber-800">{{ session('info') }}</div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Acara</p>
                <h1 class="mt-2 text-2xl font-bold">{{ $event->name }}</h1>
                <p class="mt-3 text-gray-600">{{ $event->description }}</p>
                <p class="mt-5 font-medium">
                    Sisa tiket:
                    <span class="{{ $event->quota > 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $event->quota }}
                    </span>
                </p>

                @if ($event->quota > 0)
                    <form action="{{ route('booking.store') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <button type="submit" class="rounded-md bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">
                            Pesan tiket
                        </button>
                    </form>
                @else
                    <p class="mt-6 rounded-md bg-red-50 p-4 text-red-700">Tiket sudah habis.</p>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>