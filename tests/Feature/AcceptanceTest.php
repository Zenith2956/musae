<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AcceptanceTest extends TestCase
{
    public function test_application_main_pages_are_accessible()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/dashboard')->assertStatus(200);
        $this->get('/calendar')->assertStatus(200);
        $this->get('/library')->assertStatus(200);
        $this->get('/historique')->assertStatus(200);
        $this->get('/messagerie')->assertStatus(200);
    }
}
