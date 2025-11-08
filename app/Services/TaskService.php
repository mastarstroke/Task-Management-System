<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function list($user, $filters = [])
    {
        $query = Task::where('user_id', $user->id);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 10;

        return $query->latest()->paginate($perPage);
    }


    public function store(array $data, $user)
    {
        try {
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'user_id' => $user->id,
            ]);

            activity_log('Task created', ['title' => $task->title]);
            return $task;
        } catch (\Throwable $e) {
            throw new \Exception('Unable to create task. Please try again.');
        }
    }

    public function show($id, $user)
    {
        $task = Task::where('user_id', $user->id)->find($id);
        if (!$task) throw new \Exception('Task not found.');
        return $task;
    }

    public function update($id, array $data, $user)
    {
        $task = Task::where('user_id', $user->id)->find($id);
        if (!$task) throw new \Exception('Task not found.');

        try {
            $task->update($data);
            activity_log('Task updated', ['title' => $task->title]);
            return $task;
        } catch (\Throwable $e) {
            throw new \Exception('Unable to update task.');
        }
    }

    public function delete($id, $user)
    {
        $task = Task::where('user_id', $user->id)->find($id);
        if (!$task) throw new \Exception('Task not found.');

        try {
            $task->delete();
            activity_log('Task deleted', ['title' => $task->title]);
            return true;
        } catch (\Throwable $e) {
            throw new \Exception('Unable to delete task.');
        }
    }
}
