<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class SheetApiTest extends TestCase
{
    public function test_get_library()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/library');
        $response->assertStatus(200);
    }
}
