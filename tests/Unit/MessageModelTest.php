<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Message;
use App\Models\Conversation;

class MessageModelTest extends TestCase
{
    public function test_message_belongs_to_conversation()
    {
        $message = Message::factory()->create();
        $this->assertInstanceOf(Conversation::class, $message->conversation);
    }
}
