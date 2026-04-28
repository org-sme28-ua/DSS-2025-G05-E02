<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Billetera;
use App\Models\Apuesta;
use App\Models\Mensaje;
use App\Models\Juego;
use App\Models\Chat;

class UserRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_one_billetera(): void
    {
        $user = User::factory()->create();
        $wallet = Billetera::factory()->for($user)->create();

        $this->assertNotNull($user->billetera);
        $this->assertInstanceOf(Billetera::class, $user->billetera);
        $this->assertEquals($wallet->id, $user->billetera->id);
    }

    public function test_user_has_many_apuestas(): void
    {
        $user = User::factory()->create();
        $juego = Juego::factory()->create();

        $a1 = Apuesta::factory()->for($user)->for($juego)->create();
        $a2 = Apuesta::factory()->for($user)->for($juego)->create();

        $this->assertCount(2, $user->apuestas);
        $this->assertTrue($user->apuestas->pluck('id')->contains($a1->id));
        $this->assertTrue($user->apuestas->pluck('id')->contains($a2->id));
    }

    public function test_user_has_many_mensajes_enviados_y_recibidos(): void
    {
        $emisor = User::factory()->create();
        $receptor = User::factory()->create();
        $chat = Chat::factory()->create();

        $m1 = Mensaje::factory()->for($chat)->create([
            'emisor_id' => $emisor->id,
            'receptor_id' => $receptor->id,
        ]);

        $m2 = Mensaje::factory()->for($chat)->create([
            'emisor_id' => $receptor->id,
            'receptor_id' => $emisor->id,
        ]);

        $this->assertCount(1, $emisor->mensajesEnviados);
        $this->assertEquals($m1->id, $emisor->mensajesEnviados->first()->id);

        $this->assertCount(1, $emisor->mensajesRecibidos);
        $this->assertEquals($m2->id, $emisor->mensajesRecibidos->first()->id);
    }
}
