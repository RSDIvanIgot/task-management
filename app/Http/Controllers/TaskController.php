<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest; // Import the CreateTaskRequest
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function index()
    {
        $tasks = $this->taskRepository->all();
        return response()->json($tasks);
    }

    public function store(CreateTaskRequest $request)
    {
        $task = $this->taskRepository->create($request->all());

        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        return response()->json(['task' => $task], 200);
    }

    public function update(Request $request, $id)
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $task = $this->taskRepository->update($task, $request->all());

        return response()->json($task, 200);
    }

    public function destroy($id)
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $this->taskRepository->delete($task);

        return response()->json(null, 204);
    }

    public function reorder(Request $request)
    {
        $updatedTasks = $request->input('tasks');
        $this->taskRepository->reorder($updatedTasks);

        return response()->json(['message' => 'Task order updated successfully']);
    }
}
