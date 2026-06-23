<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Conversation;

class MessagerieIntegrationTest extends TestCase
{
    public function test_user_can_create_conversation_and_send_message()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        // Créer une conversation vide
        $conversation = Conversation::factory()->create();
        $conversation->users()->attach($user->id);

        $response = $this->postJson("/messagerie/{$conversation->id}/messages", [
            'content' => 'Hello world'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            'content' => 'Hello world',
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
        ]);
    }
}
