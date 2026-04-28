<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\Billetera;
use App\Models\Apuesta;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RelacionesUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_puede_tener_muchos_mensajes()
    {
        $user = User::factory()->create();

        Mensaje::factory()->count(3)->create([
            'emisor_id' => $user->id
        ]);

        $this->assertCount(3, $user->mensajes);
    }

    public function test_user_puede_tener_muchos_chats()
    {
        $user = User::factory()->create();

        Chat::factory()->count(2)->create([
            'user_id' => $user->id
        ]);

        $this->assertCount(2, $user->chats);
    }

    public function test_user_tiene_una_billetera()
    {
        $user = User::factory()->create();

        $billetera = Billetera::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(Billetera::class, $user->billetera);
    }

    public function test_user_puede_tener_muchas_apuestas()
    {
        $user = User::factory()->create();

        Apuesta::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $this->assertCount(3, $user->apuestas);
    }

    public function test_eliminar_user_cascade_en_billetera()
    {
        $user = User::factory()->create();

        Billetera::factory()->create([
            'user_id' => $user->id
        ]);

        $user->delete();

        $this->assertDatabaseCount('billeteras', 0);
    }
}