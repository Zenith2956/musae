<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class RegressionTest extends TestCase
{
    public function test_calendar_route_still_exists()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/calendar/events');
        $response->assertStatus(200);
    }

    public function test_messagerie_route_still_exists()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/messagerie');
        $response->assertStatus(200);
    }
}
