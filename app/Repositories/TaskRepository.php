<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function all()
    {
        return Task::orderBy('order')->get();
    }

    public function create(array $data)
    {
        return Task::create($data);
    }

    public function find($id)
    {
        return Task::find($id);
    }

    public function update(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task)
    {
        $task->delete();
    }

    public function reorder(array $tasks)
    {
        foreach ($tasks as $taskData) {
            $taskId = $taskData['id'];
            $newOrder = $taskData['order'];
            $column = $taskData['column'];

            $task = Task::findOrFail($taskId);

            if ($task) {
                $task->order = $newOrder;
                $task->column_id = $column;
                $task->save();
            }
        }
    }
}
