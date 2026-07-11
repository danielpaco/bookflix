<?php

namespace App\Http\Controllers\Api;


use App\Support\ApiResponse;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(
        AuthService $authService
    ){
        $this->authService = $authService;
    }

    public function register(RegisteRequest $request)
    {
        $result = $this->authService
            ->register(
                $request->validated()
            );

        return ApiResponse::success(
            [
                'token' => $result['token'],
                'user' => new UserResource(
                    $result['user']
                )
            ],
            'User registered successfully',
            201
        );
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService
            ->login(
                $request->validated()
            );

        return ApiResponse::success(
            [
                'token' => $result['token'],
                'user' => new UserResource(
                    $result['user']
                )
            ],
            'Login successful'
        );
    }

    public function logout(Request $request)
    {
        $this->authService
            ->logout($request->user());

        return ApiResponse::success(
            null,
            'Logout successful'
        );
    }

    public function me(Request $request)
    {
        return ApiResponse::success(

            new UserResource(
                $request->user()
            )

        );
    }
}