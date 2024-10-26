<?php

namespace App\Resources\Auth;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    public function __construct(private string $token)
    {
        parent::__construct(null);
    }

    public function toArray($request)
    {
        return [
            'access_token' => $this->token,
            'token_type' => 'Bearer',
        ];
    }
}
