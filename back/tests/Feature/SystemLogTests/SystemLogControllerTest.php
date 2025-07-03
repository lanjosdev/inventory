<?php

namespace Tests\Feature\SystemLogTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\SystemLog;
use App\Models\User;

class SystemLogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_logs()
    {
        SystemLog::factory()->count(15)->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/system-logs?per_page=10');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data',
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    public function test_index_returns_unauthorized_if_not_authenticated()
    {
        $response = $this->getJson('/api/system-logs');
        $response->assertStatus(401);
    }

    public function test_show_returns_log_data()
    {
        $user = User::factory()->create();
        $log = SystemLog::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/system-logs/' . $log->id);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Log encontrado.',
                'data' => [
                    'id' => $log->id
                ]
            ]);
    }

    public function test_show_returns_unauthorized_if_not_authenticated()
    {
        $log = SystemLog::factory()->create();
        $response = $this->getJson('/api/system-logs/' . $log->id);
        $response->assertStatus(401);
    }

    public function test_show_returns_not_found_for_invalid_id()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/system-logs/999999');
        $response->assertStatus(404);
    }
}