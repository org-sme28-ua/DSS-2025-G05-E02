<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Juego;
use App\Models\Apuesta;

class JuegoRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_juego_has_many_apuestas(): void
    {
        $juego = Juego::factory()->create();
        Apuesta::factory()->count(3)->for($juego)->create(); // user lo rellena el factory

        $this->assertCount(3, $juego->apuestas);
        $this->assertInstanceOf(Apuesta::class, $juego->apuestas->first());
    }
}
