<?php

namespace App\RequestValidators\Tasks;

use App\Enums\Tasks\TaskPriorityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', Rule::in(TaskPriorityEnum::getValues())],
            'due_date' => ['required', 'date'],
            'auto_complete_on_due_date' => ['nullable', 'boolean'],
        ];
    }
}
