<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data)
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            activity_log('User registered', ['email' => $user->email]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return ['user' => $user, 'token' => $token];

        } catch (\Throwable $e) {
            throw new \Exception('Unable to complete registration. Please try again.');
        }
    }

    public function login(array $data)
    {
        try {
            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid email or password.'],
                ]);
            }

            activity_log('User logged in', ['email' => $user->email]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return ['user' => $user, 'token' => $token];

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \Exception('Unable to process login. Please try again.');
        }
    }

    public function logout($user)
    {
        try {
            activity_log('User logged out', ['email' => $user->email]);
            $user->currentAccessToken()->delete();
            return true;

        } catch (\Throwable $e) {
            throw new \Exception('Logout failed. Please try again.');
        }
    }
}
