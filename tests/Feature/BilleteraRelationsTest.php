<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Billetera;
use App\Models\User;

class BilleteraRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_billetera_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $wallet = Billetera::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $wallet->user);
        $this->assertEquals($user->id, $wallet->user->id);
    }
}
