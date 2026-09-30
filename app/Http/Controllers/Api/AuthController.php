<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Authenticate user and return JWT token.
     */
    public function login(LoginRequest $request)
    {
        // To be implemented by developer
    }

    /**
     * Get the authenticated User profile.
     */
    public function me()
    {
        // To be implemented by developer
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        // To be implemented by developer
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        // To be implemented by developer
    }
}
