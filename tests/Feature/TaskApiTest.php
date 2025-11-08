<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $headers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $token = $this->user->createToken('auth_token')->plainTextToken;
        $this->headers = ['Authorization' => 'Bearer ' . $token];
    }

    /** @test */
    public function user_can_create_a_task()
    {
        $payload = [
            'title' => 'Test Task',
            'description' => 'Task description',
            'status' => 'pending',
        ];

        $this->json('POST', '/api/tasks', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'title', 'description', 'status', 'created_at', 'updated_at']
            ]);
    }

    /** @test */
    public function user_can_list_their_tasks_with_filter()
    {
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'completed']);

        $this->json('GET', '/api/tasks?status=pending', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'pending'])
            ->assertJsonMissing(['status' => 'completed']);
    }
}
