<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Resource;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_booking()
    {
        $user = User::factory()->create();
        $resource = Resource::factory()->create();

        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/bookings', [
            'resource_id' => $resource->id,
            'starts_at'   => now()->addHours(2),
            'ends_at'     => now()->addHours(3),
        ]);

        $response->assertStatus(201);
    }

    public function test_cannot_book_overlapping_time()
    {
        $user = User::factory()->create();
        $resource = Resource::factory()->create();

        Booking::factory()->create([
            'resource_id' => $resource->id,
            'starts_at'   => '2026-03-20 14:00:00',
            'ends_at'     => '2026-03-20 15:00:00',
        ]);

        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/bookings', [
            'resource_id' => $resource->id,
            'starts_at'   => '2026-03-20 14:30:00',
            'ends_at'     => '2026-03-20 15:30:00',
        ]);

        $response->assertStatus(409);
    }
}