<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\UserLoginDTO;
use App\DTOs\Auth\UserRegisterDTO;
use App\Http\Controllers\Controller;
use App\RequestValidators\UserLoginRequest;
use App\RequestValidators\UserRegisterRequest;
use App\Resources\Auth\UserLoginResource;
use App\Resources\Auth\UserRegisterResource;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(UserRegisterRequest $request)
    {
        $userRegisterDTO = UserRegisterDTO::makeFromRequest($request);

        $token = $this->authService->register($userRegisterDTO);

        return response()->json(new UserRegisterResource($token), 201);
    }

    public function login(UserLoginRequest $request)
    {
        $userLoginDTO = UserLoginDTO::makeFromRequest($request);

        $token = $this->authService->login($userLoginDTO);

        return response()->json(new UserLoginResource($token), 200);
    }

    public function logout()
    {
        $this->authService->logout(auth()->user());

        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }
}
