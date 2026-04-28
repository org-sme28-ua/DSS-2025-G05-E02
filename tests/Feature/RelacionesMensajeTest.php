<?php
// tests/Feature/RelacionesMensajeTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Mensaje;
use App\Models\User;
use App\Models\Chat;

class RelacionesMensajeTest extends TestCase
{
    use RefreshDatabase;

    public function test_mensaje_pertenece_a_un_emisor()
    {
        $usuario = User::factory()->create();
        $mensaje = Mensaje::factory()->create([
            'emisor_id' => $usuario->id
        ]);
        
        $this->assertInstanceOf(User::class, $mensaje->emisor);
        $this->assertEquals($usuario->id, $mensaje->emisor->id);
    }

    public function test_mensaje_pertenece_a_un_chat()
    {
        $chat = Chat::factory()->create();
        $mensaje = Mensaje::factory()->create([
            'chat_id' => $chat->id
        ]);
        
        $this->assertInstanceOf(Chat::class, $mensaje->chat);
        $this->assertEquals($chat->id, $mensaje->chat->id);
    }

    public function test_campos_boolean_y_datetime_funcionan_correctamente()
    {
        $mensaje = Mensaje::factory()->create([
            'editado' => true,
            'created_at' => now()
        ]);
        
        $this->assertIsBool($mensaje->editado);
        $this->assertTrue($mensaje->editado);
        $this->assertInstanceOf(\DateTime::class, $mensaje->created_at);
    }
}