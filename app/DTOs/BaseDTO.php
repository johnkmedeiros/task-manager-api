<?php

namespace App\DTOs;

use Illuminate\Http\Request;

abstract class BaseDTO
{
    abstract public static function makeFromRequest(Request $request): self;

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
