<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(
                    $data['password']
                )
            ]);

            return [
                'token' => $user
                    ->createToken('app')
                    ->plainTextToken,
                'user' => $user
            ];

        });
    }

    public function login(array $data): array
    {
        $user = User::where(
            'email',
            $data['email']
        )->first();

        if (
            ! $user ||
            ! Hash::check(
                $data['password'],
                $user->password
            )
        ){
            throw ValidationException::withMessages([
                'email'=>[
                    'Invalid credentials.'
                ]
            ]);
        }

        return [
            'token'=>
                $user
                    ->createToken('app')
                    ->plainTextToken,
            'user'=>$user
        ];
    }

    public function logout(User $user): void
    {
        $user
            ->currentAccessToken()
            ?->delete();
    }
}