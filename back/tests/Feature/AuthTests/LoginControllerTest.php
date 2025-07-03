<?php

namespace Tests\Feature\AuthTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'login@email.com',
            'password' => Hash::make('senhaSegura123'),
        ]);

        $payload = [
            'email' => 'login@email.com',
            'password' => 'senhaSegura123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
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
                    ],
                    'level'
                ]
            ]);
    }

    public function test_login_invalid_credentials()
    {
        $payload = [
            'email' => 'naoexiste@email.com',
            'password' => 'senhaErrada',
        ];
        $response = $this->postJson('/api/login', $payload);
        $response->assertStatus(401);
    }

    public function test_login_validation_error()
    {
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422);
    }
}
