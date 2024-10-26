<?php

namespace App\DTOs\Tasks;

use App\DTOs\BaseDTO;
use App\Enums\Tasks\TaskPriorityEnum;
use App\Models\Task;
use Illuminate\Http\Request;

class CreateTaskDTO extends BaseDTO
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?string $priority,
        public string $due_date,
        public ?bool $auto_complete_on_due_date
    ) {
    }

    public static function makeFromRequest(Request $request): self
    {
        return new self(
            title: $request->title,
            description: $request->filled('description') ? $request->description : null,
            priority: $request->filled('priority') ? $request->priority : TaskPriorityEnum::MEDIUM,
            due_date: $request->due_date,
            auto_complete_on_due_date: $request->filled('auto_complete_on_due_date') ? $request->auto_complete_on_due_date : false
        );
    }
}
