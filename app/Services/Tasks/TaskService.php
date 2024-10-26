<?php

namespace App\Services\Tasks;

use App\DTOs\Tasks\CreateTaskDTO;
use App\DTOs\Tasks\UpdateTaskDTO;
use App\Enums\Tasks\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function getPaginatedTasksFromUser(User $user): LengthAwarePaginator
    {
        return $user->tasks()->paginate(30);
    }

    public function getTaskFromUser(User $user, int $taskId): Task
    {
        return $user->tasks()->findOrFail($taskId);
    }

    public function store(CreateTaskDTO $createTaskDTO, User $user): Task
    {
        return Task::create([
            'user_id' => $user->id,
            'title' => $createTaskDTO->title,
            'description' => $createTaskDTO->description,
            'status' => TaskStatusEnum::PENDING,
            'priority' => $createTaskDTO->priority,
            'due_date' => $createTaskDTO->due_date,
            'auto_complete_on_due_date' => $createTaskDTO->auto_complete_on_due_date
        ]);
    }

    public function update(UpdateTaskDTO $updateTaskDTO, User $user, int $taskId): Task
    {
        $task = $user->tasks()->findOrFail($taskId);

        $task->title = $updateTaskDTO->title ?? $task->title;
        $task->description = $updateTaskDTO->description ?? $task->description;
        $task->status = $updateTaskDTO->status ?? $task->status;
        $task->priority = $updateTaskDTO->priority ?? $task->priority;
        $task->due_date = $updateTaskDTO->due_date ?? $task->due_date;
        $task->auto_complete_on_due_date = $updateTaskDTO->auto_complete_on_due_date ?? $task->auto_complete_on_due_date;

        $task->save();

        return $task;
    }

    public function destroy(User $user, int $taskId): void
    {
        $task = $user->tasks()->findOrFail($taskId);

        $task->delete();
    }
}
