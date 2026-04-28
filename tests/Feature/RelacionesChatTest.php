<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Chat;
use App\Models\Mensaje;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RelacionesChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_pertenece_a_un_user()
    {
        $user = User::factory()->create();

        $chat = Chat::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $chat->user);
        $this->assertEquals($user->id, $chat->user->id);
    }

    public function test_chat_puede_tener_muchos_mensajes()
    {
        $chat = Chat::factory()->create();

        Mensaje::factory()->count(3)->create([
            'chat_id' => $chat->id
        ]);

        $this->assertCount(3, $chat->mensajes);
    }

    public function test_mensajes_se_eliminan_al_eliminar_chat()
    {
        $chat = Chat::factory()->create();

        Mensaje::factory()->count(3)->create([
            'chat_id' => $chat->id
        ]);

        $chat->delete();

        $this->assertDatabaseCount('mensajes', 0);
    }
}