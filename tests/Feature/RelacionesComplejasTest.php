<?php
// tests/Feature/RelacionesComplejasTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\Billetera;
use App\Models\Juego;
use App\Models\Apuesta;

class RelacionesComplejasTest extends TestCase
{
    use RefreshDatabase;

    public function test_ciclo_completo_usuario_chat_mensajes()
    {
        // Crear usuario
        $usuario = User::factory()->create();
        
        // Usuario crea un chat
        $chat = Chat::factory()->create([
            'user_id' => $usuario->id,
            'nombre' => 'Chat de Prueba'
        ]);
        
        // Usuario envía mensajes en ese chat
        Mensaje::factory()->count(3)->create([
            'emisor_id' => $usuario->id,
            'chat_id' => $chat->id
        ]);
        
        // Verificar relaciones en cadena
        $this->assertEquals('Chat de Prueba', $usuario->chats->first()->nombre);
        $this->assertCount(3, $usuario->chats->first()->mensajes);
        $this->assertEquals($usuario->id, $usuario->chats->first()->mensajes->first()->emisor->id);
    }

    public function test_usuario_con_apuestas_y_billetera()
    {
        $usuario = User::factory()->create();
        
        // Crear billetera
        Billetera::factory()->create([
            'user_id' => $usuario->id,
            'saldoDisponible' => 1000,
            'moneda' => 'EUR'
        ]);
        
        // Crear juegos
        $juego1 = Juego::factory()->create(['nombre' => 'Ruleta']);
        $juego2 = Juego::factory()->create(['nombre' => 'Blackjack']);
        
        // Crear apuestas
        Apuesta::factory()->create([
            'user_id' => $usuario->id,
            'juego_id' => $juego1->id,
            'monto' => 100,
            'estado' => 'pendiente'
        ]);
        
        Apuesta::factory()->create([
            'user_id' => $usuario->id,
            'juego_id' => $juego2->id,
            'monto' => 200,
            'estado' => 'ganada'
        ]);
        
        // Verificar relaciones
        $this->assertEquals(1000, $usuario->billetera->saldoDisponible);
        $this->assertCount(2, $usuario->apuestas);
        $this->assertEquals('Ruleta', $usuario->apuestas[0]->juego->nombre);
        $this->assertEquals('Blackjack', $usuario->apuestas[1]->juego->nombre);
    }
}