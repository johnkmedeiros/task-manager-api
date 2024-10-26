<?php

namespace App\DTOs\Tasks;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class UpdateTaskDTO extends BaseDTO
{
    public function __construct(
        public ?string $title,
        public ?string $description,
        public ?string $status,
        public ?string $priority,
        public ?string $due_date,
        public ?bool $auto_complete_on_due_date
    ) {
    }

    public static function makeFromRequest(Request $request): self
    {
        return new self(
            title: $request->filled('title') ? $request->title : null,
            description: $request->filled('description') ? $request->description : null,
            status: $request->filled('status') ? $request->status : null,
            priority: $request->filled('priority') ? $request->priority : null,
            due_date: $request->filled('due_date') ? $request->due_date : null,
            auto_complete_on_due_date: $request->filled('auto_complete_on_due_date') ? $request->auto_complete_on_due_date : null
        );
    }
}
