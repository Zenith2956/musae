<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GenericInstrument;
use App\Models\Sheet;

class CalendarApiTest extends TestCase
{
    public function test_get_events()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/calendar/events');
        $response->assertStatus(200);
    }

    public function test_create_event()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        // CRÉER les vraies dépendances
        $instrument = GenericInstrument::factory()->create();
        $sheet = Sheet::factory()->create();

        $response = $this->post('/calendar/events', [
            'title' => 'Test event',
            'start' => now()->toDateTimeString(),
            'end' => now()->addHour()->toDateTimeString(),
            'end_training' => now()->addHour()->toDateTimeString(),
            'instrument_id' => $instrument->id,
            'sheet_id' => $sheet->id,
            'link' => 'https://example.com/video.mp4',
        ]);


        $response->assertStatus(201);
    }
}
