<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $data = $this->authService->register($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful.',
                'user' => new UserResource($data['user']),
                'token' => $data['token']
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $email = (string) $request->email;
        $key = 'login-attempts:' . Str::lower($email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many login attempts. Please try again in 1 minute.'
            ], 429);
        }

        try {
            $data = $this->authService->login($request->validated());
            RateLimiter::clear($key);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful.',
                'user' => new UserResource($data['user']),
                'token' => $data['token']
            ]);

        } catch (ValidationException $e) {
            RateLimiter::hit($key, 60);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 401);

        } catch (\Throwable $e) {
            RateLimiter::hit($key, 60);
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
