<?php

namespace Tests\Feature\AuthTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success_only_admin()
    {
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $payload = [
            'name' => 'Usuário Teste',
            'email' => 'teste@email.com',
            'password' => 'senhaSegura123',
        ];
        $response = $this->actingAs($admin)->postJson('/api/register', $payload);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);
        $this->assertDatabaseHas('users', [
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
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $response = $this->actingAs($admin)->postJson('/api/register', []);
        $response->assertStatus(422);
    }
}