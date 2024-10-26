<?php

namespace App\Services\Auth;

use App\DTOs\Auth\UserLoginDTO;
use App\DTOs\Auth\UserRegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(UserRegisterDTO $userRegisterDTO): string
    {
        $user = User::create([
            'name' => $userRegisterDTO->name,
            'email' => $userRegisterDTO->email,
            'password' => bcrypt($userRegisterDTO->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $token;
    }

    public function login(UserLoginDTO $userLoginDto): string
    {
        $user = User::where('email', $userLoginDto->email)->first();

        if (!$user || !Hash::check($userLoginDto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return $token;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
