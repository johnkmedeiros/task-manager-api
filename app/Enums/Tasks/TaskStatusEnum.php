<?php

namespace App\Enums\Tasks;

use BenSampo\Enum\Enum;

final class TaskStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';
    public const OVERDUE = 'overdue';
}
