<?php

namespace App\Http\Controllers\Tasks;

use App\DTOs\Tasks\CreateTaskDTO;
use App\DTOs\Tasks\UpdateTaskDTO;
use App\Http\Controllers\Controller;
use App\RequestValidators\Tasks\CreateTaskRequest;
use App\RequestValidators\Tasks\UpdateTaskRequest;
use App\Resources\Tasks\TaskResource;
use App\Services\Tasks\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
    }

    public function index(Request $request)
    {
        $tasks = $this->taskService->getPaginatedTasksFromUser(auth()->user());

        return TaskResource::collection($tasks);
    }

    public function show(Request $request, int $taskId)
    {
        $task = $this->taskService->getTaskFromUser(auth()->user(), $taskId);

        return response()->json(['data' => new TaskResource($task)], 200);
    }

    public function store(CreateTaskRequest $request)
    {
        $userRegisterDTO = CreateTaskDTO::makeFromRequest($request);

        $task = $this->taskService->store($userRegisterDTO, auth()->user());

        return response()->json(['data' => new TaskResource($task)], 201);
    }

    public function update(UpdateTaskRequest $request, int $taskId)
    {
        $updateTaskDTO = UpdateTaskDTO::makeFromRequest($request);

        $task = $this->taskService->update($updateTaskDTO, auth()->user(), $taskId);

        return response()->json(['data' => new TaskResource($task)], 200);
    }

    public function destroy(Request $request, int $taskId)
    {
        $this->taskService->destroy(auth()->user(), $taskId);

        return response()->json([], 200);
    }
}
