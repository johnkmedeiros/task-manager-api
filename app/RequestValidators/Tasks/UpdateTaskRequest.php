<?php

namespace App\RequestValidators\Tasks;

use App\Enums\Tasks\TaskPriorityEnum;
use App\Enums\Tasks\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', Rule::in(TaskStatusEnum::getValues())],
            'priority' => ['nullable', 'string', Rule::in(TaskPriorityEnum::getValues())],
            'due_date' => ['nullable', 'date'],
            'auto_complete_on_due_date' => ['nullable', 'boolean'],
        ];
    }
}
