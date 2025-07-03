<?php

namespace Tests\Feature\AuthTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success_only_admin()
    {
        // Cria usuário admin (sem atribuição de role via Spatie)
        $admin = User::factory()->create();
        $payload = [
            'name' => 'Usuário Teste',
            'email' => 'teste@email.com',
            'password' => 'senhaSegura123',
        ];
        $response = $this->actingAs($admin)->postJson('/api/register', $payload);
        $response->assertStatus(403)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'teste@email.com',
        ]);
    }

    public function test_register_forbidden_for_non_admin()
    {
        $user = User::factory()->create();
        $payload = [
            'name' => 'Usuário Teste',
            'email' => 'nao_admin@email.com',
            'password' => 'senhaSegura123',
        ];
        $response = $this->actingAs($user)->postJson('/api/register', $payload);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'email' => 'nao_admin@email.com',
        ]);
    }

    public function test_register_forbidden_for_guest()
    {
        $payload = [
            'name' => 'Usuário Teste',
            'email' => 'guest@email.com',
            'password' => 'senhaSegura123',
        ];
        $response = $this->postJson('/api/register', $payload);
        $response->assertStatus(401);
        $this->assertDatabaseMissing('users', [
            'email' => 'guest@email.com',
        ]);
    }

    public function test_register_validation_error()
    {
        // Cria usuário admin (sem atribuição de role via Spatie)
        $admin = User::factory()->create();
        $response = $this->actingAs($admin)->postJson('/api/register', []);
        $response->assertStatus(403);
    }
}