<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Apuesta;
use App\Models\User;
use App\Models\Juego;

class ApuestasRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_apuesta_belongs_to_user_and_juego(): void
    {
        $user = User::factory()->create();
        $juego = Juego::factory()->create();

        $apuesta = Apuesta::factory()->for($user)->for($juego)->create();

        $this->assertInstanceOf(User::class, $apuesta->user);
        $this->assertEquals($user->id, $apuesta->user->id);

        $this->assertInstanceOf(Juego::class, $apuesta->juego);
        $this->assertEquals($juego->id, $apuesta->juego->id);
    }
}
