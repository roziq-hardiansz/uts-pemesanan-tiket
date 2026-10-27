<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendTicketConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $bookingId
    ) {}

    public function handle(): void
    {
        DB::transaction(function (): void {
            $booking = Booking::with(['user', 'event'])
                ->lockForUpdate()
                ->findOrFail($this->bookingId);

            if ($booking->confirmation_sent_at !== null) {
                return;
            }

            Mail::raw(
                "Pemesanan tiket berhasil.\n\n".
                "Nama acara: {$booking->event->name}\n".
                "Kode tiket: {$booking->ticket_code}\n".
                "Status: {$booking->status}\n\n".
                'Simpan kode tiket ini sebagai bukti pemesanan.',
                function ($message) use ($booking): void {
                    $message
                        ->to($booking->user->email)
                        ->subject('Bukti Pemesanan Tiket Kampus');
                }
            );

            $booking->forceFill([
                'confirmation_sent_at' => now(),
            ])->save();
        });
    }
}
