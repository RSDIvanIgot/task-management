<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase; // Reset the database before each test

    public function test_can_create_task()
    {
        $data = [
            'title' => 'New Task',
            'description' => 'Task description',
            'due_date' => '2023-12-31', // Example due date
            'status' => 'todo',
            'column_id' => 1, // Example column ID
            'order' => 1, // Example order
        ];

        $response = $this->post('/api/tasks', $data);

        $response->assertStatus(201);
        $response->assertJson($data);

        $this->assertDatabaseHas('tasks', $data);
    }

    public function test_can_get_single_task()
    {
        $task = Task::create([
            'title' => 'Manually Created Task',
            'description' => 'Task description',
            'due_date' => '2023-12-31',
            'status' => 'todo',
            'column_id' => 1,
            'order' => 1,
        ]);

        $response = $this->get("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'task' => $task->toArray(),
        ]);
    }

    public function test_can_get_all_tasks()
    {
        $tasks = [
            [
                'title' => 'Task 1',
                'description' => 'Description 1',
                'due_date' => '2023-12-31',
                'status' => 'todo',
                'column_id' => 1,
                'order' => 1,
            ],
        ];

        Task::insert($tasks);

        $response = $this->get('/api/tasks');

        $response->assertStatus(200);

    }

    public function test_can_update_task()
    {
        $task = Task::create([
            'title' => 'Manually Created Task',
            'description' => 'Task description',
            'due_date' => '2023-12-31',
            'status' => 'todo',
            'column_id' => 1,
            'order' => 1,
        ]);

        $data = [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'due_date' => '2023-12-31',
            'status' => 'inProgress',
            'column_id' => 2,
            'order' => 2,
        ];

        $response = $this->put("/api/tasks/{$task->id}", $data);

        $response->assertStatus(200);
        $response->assertJson($data);

        $this->assertDatabaseHas('tasks', $data);
    }

    public function test_can_delete_task()
    {
        $task = Task::create([
            'title' => 'Manually Created Task',
            'description' => 'Task description',
            'due_date' => '2023-12-31',
            'status' => 'todo',
            'column_id' => 1,
            'order' => 1,
        ]);

        $response = $this->delete("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
