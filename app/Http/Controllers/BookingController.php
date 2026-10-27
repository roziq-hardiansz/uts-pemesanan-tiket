<?php

namespace App\Http\Controllers;

use App\Jobs\SendTicketConfirmation;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create()
    {
        $event = Event::first();

        abort_if(! $event, 404, 'Acara belum tersedia.');

        return view('bookings.create', compact('event'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        $user = $request->user();

        try {
            $booking = DB::transaction(function () use (
                $request,
                $user
            ) {
                // Mengunci baris acara selama transaksi
                // agar pemesanan bersamaan tidak melewati kuota.
                $event = Event::whereKey($request->event_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($event->quota < 1) {
                    abort(422, 'Maaf, tiket sudah habis.');
                }

                // Mengurangi kuota dalam transaksi yang sama.
                $event->decrement('quota');

                return Booking::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'ticket_code' => (string) Str::uuid(),
                    'status' => 'confirmed',
                ]);
            });
        } catch (QueryException $e) {
            // Unique constraint mencegah akun memesan
            // tiket acara yang sama lebih dari sekali.
            if (Booking::where('user_id', $user->id)
                ->where('event_id', $request->event_id)
                ->exists()) {
                return redirect()
                    ->route('booking.index')
                    ->with('info', 'Kamu sudah memesan tiket acara ini.');
            }

            throw $e;
        }

        // Dijalankan setelah transaksi database berhasil.
        SendTicketConfirmation::dispatch($booking->id)->afterCommit();

        return redirect()
            ->route('booking.show', $booking)
            ->with('success', 'Pemesanan berhasil. Bukti tiket sedang dikirim melalui email.');
    }

    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with('event')
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function show(Request $request, Booking $booking)
    {
        // Tiket hanya dapat dilihat oleh pemiliknya.
        abort_unless(
            $booking->user_id === $request->user()->id,
            403,
            'Kamu tidak berhak melihat tiket ini.'
        );

        $booking->load('event');

        return view('bookings.show', compact('booking'));
    }
}
