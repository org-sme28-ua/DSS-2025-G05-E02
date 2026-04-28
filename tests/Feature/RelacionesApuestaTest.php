<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Apuesta;
use App\Models\Juego;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RelacionesApuestaTest extends TestCase
{
    use RefreshDatabase;

    public function test_apuesta_pertenece_a_un_user()
    {
        $user = User::factory()->create();

        $apuesta = Apuesta::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $apuesta->user);
        $this->assertEquals($user->id, $apuesta->user->id);
    }

    public function test_apuesta_pertenece_a_un_juego()
    {
        $juego = Juego::factory()->create();

        $apuesta = Apuesta::factory()->create([
            'juego_id' => $juego->id
        ]);

        $this->assertInstanceOf(Juego::class, $apuesta->juego);
        $this->assertEquals($juego->id, $apuesta->juego->id);
    }

    public function test_user_puede_tener_multiples_apuestas_en_diferentes_juegos()
    {
        $user = User::factory()->create();

        $juegos = Juego::factory()->count(2)->create();

        Apuesta::factory()->create([
            'user_id' => $user->id,
            'juego_id' => $juegos[0]->id
        ]);

        Apuesta::factory()->create([
            'user_id' => $user->id,
            'juego_id' => $juegos[1]->id
        ]);

        $this->assertCount(2, $user->apuestas);
    }
}