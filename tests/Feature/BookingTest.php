<?php

namespace Tests\Feature;

use App\Jobs\SendTicketConfirmation;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_must_log_in_before_booking(): void
    {
        $event = Event::create([
            'name' => 'Festival Teknologi Kampus',
            'quota' => 10,
        ]);

        $this->post(route('booking.store'), ['event_id' => $event->id])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_a_user_can_book_once_and_email_is_queued(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $event = Event::create([
            'name' => 'Festival Teknologi Kampus',
            'quota' => 2,
        ]);

        $this->actingAs($user)
            ->post(route('booking.store'), ['event_id' => $event->id])
            ->assertRedirect();

        $booking = Booking::firstOrFail();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'quota' => 1]);
        $this->assertNull($booking->confirmation_sent_at);
        Queue::assertPushed(SendTicketConfirmation::class, 1);

        $this->post(route('booking.store'), ['event_id' => $event->id])
            ->assertRedirect(route('booking.index'))
            ->assertSessionHas('info');

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseHas('events', ['id' => $event->id, 'quota' => 1]);
        Queue::assertPushed(SendTicketConfirmation::class, 1);
    }

    public function test_booking_is_rejected_when_event_is_sold_out(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'name' => 'Festival Teknologi Kampus',
            'quota' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('booking.store'), ['event_id' => $event->id])
            ->assertUnprocessable();

        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseHas('events', ['id' => $event->id, 'quota' => 0]);
    }

    public function test_a_booking_receipt_is_visible_only_to_its_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $event = Event::create(['name' => 'Festival Teknologi Kampus', 'quota' => 1]);
        $booking = Booking::create([
            'user_id' => $owner->id,
            'event_id' => $event->id,
            'ticket_code' => 'ticket-owner-only',
            'status' => 'confirmed',
        ]);

        $this->actingAs($otherUser)
            ->get(route('booking.show', $booking))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('booking.show', $booking))
            ->assertOk()
            ->assertSee($booking->ticket_code);
    }

    public function test_admin_routes_are_restricted_to_administrators(): void
    {
        $participant = User::factory()->create();

        $this->actingAs($participant)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $administrator = User::factory()->create();
        $administrator->forceFill(['is_admin' => true])->save();

        $this->actingAs($administrator)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_retried_confirmation_job_does_not_send_a_second_email(): void
    {
        Mail::shouldReceive('raw')->once()->andReturnNull();

        $user = User::factory()->create();
        $event = Event::create(['name' => 'Festival Teknologi Kampus', 'quota' => 1]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_code' => 'ticket-email-once',
            'status' => 'confirmed',
        ]);

        $job = new SendTicketConfirmation((int) $booking->id);
        $job->handle();
        $job->handle();

        $this->assertNotNull($booking->fresh()->confirmation_sent_at);
    }
}
