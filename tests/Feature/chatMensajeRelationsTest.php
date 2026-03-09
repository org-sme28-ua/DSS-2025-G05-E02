<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\User;

class ChatMensajeRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_has_many_mensajes(): void
    {
        $chat = Chat::factory()->create();
        Mensaje::factory()->count(2)->for($chat)->create();

        $this->assertCount(2, $chat->mensajes);
        $this->assertInstanceOf(Mensaje::class, $chat->mensajes->first());
    }

    public function test_mensaje_belongs_to_chat_emisor_y_receptor(): void
    {
        $chat = Chat::factory()->create();
        $emisor = User::factory()->create();
        $receptor = User::factory()->create();

        $mensaje = Mensaje::factory()->for($chat)->create([
            'emisor_id' => $emisor->id,
            'receptor_id' => $receptor->id,
        ]);

        $this->assertEquals($chat->id, $mensaje->chat->id);
        $this->assertEquals($emisor->id, $mensaje->emisor->id);
        $this->assertEquals($receptor->id, $mensaje->receptor->id);
    }
}
