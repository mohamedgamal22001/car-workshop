<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $userRole = is_object($user->role) ? $user->role->value : $user->role;

        if (!in_array($userRole, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to perform this action.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
