<?php

namespace Tests\Feature\AuthTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_success()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout realizado com sucesso',
            ]);
    }

    public function test_logout_unauthenticated()
    {
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);
    }
}
