<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->middleware('auth:sanctum');
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'per_page']);

        try {
            $tasks = $this->taskService->list($request->user(), $filters);
            return response()->json([
                'status' => 'success',
                'data' => TaskResource::collection($tasks),
                'meta' => [
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(TaskStoreRequest $request)
    {
        try {
            $task = $this->taskService->store($request->validated(), $request->user());
            return response()->json([
                'status' => 'success',
                'message' => 'Task created successfully.',
                'data' => new TaskResource($task)
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id, Request $request)
    {
        try {
            $task = $this->taskService->show($id, $request->user());
            return new TaskResource($task);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(TaskUpdateRequest $request, $id)
    {
        try {
            $task = $this->taskService->update($id, $request->validated(), $request->user());
            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully.',
                'data' => new TaskResource($task)
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $this->taskService->delete($id, $request->user());
            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted successfully.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
