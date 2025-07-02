<?php

namespace Tests\Feature\AuthTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success()
    {
        $payload = [
            'name' => 'Usuário Teste',
            'email' => 'teste@email.com',
            'password' => 'senhaSegura123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
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

    public function test_register_validation_error()
    {
        $response = $this->postJson('/api/register', []);
        $response->assertStatus(422);
    }
}
