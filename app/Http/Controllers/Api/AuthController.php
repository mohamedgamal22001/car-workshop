<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $loginInput = $request->input('login');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $password = $request->input('password');

        $identifier = $loginInput ?? $email ?? $phone;

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return $this->error('Invalid credentials', Response::HTTP_UNAUTHORIZED, null, 'INVALID_CREDENTIALS');
        }

        if (!$user->is_active) {
            return $this->error('Your account is deactivated.', Response::HTTP_FORBIDDEN, null, 'ACCOUNT_DEACTIVATED');
        }

        if (!$token = auth('api')->login($user)) {
            return $this->error('Could not create token', Response::HTTP_INTERNAL_SERVER_ERROR, null, 'SERVER_ERROR');
        }

        return $this->success([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'workshop_id' => $user->workshop_id,
                'is_active' => (bool)$user->is_active,
            ],
        ], 'Login successful');
    }

    public function me(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('Unauthenticated', Response::HTTP_UNAUTHORIZED, null, 'UNAUTHENTICATED');
        }

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'workshop_id' => $user->workshop_id,
            'is_active' => (bool)$user->is_active,
        ], 'Current user profile');
    }


    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return $this->success(null, 'Successfully logged out');
    }

    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();

        return $this->success([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 'Token refreshed');
    }
}
