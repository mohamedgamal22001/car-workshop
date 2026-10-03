<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function success(mixed $data = null, string $message = 'Success', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function paginated(LengthAwarePaginator $paginator, mixed $transformedData = null, string $message = 'Success', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $transformedData ?? $paginator->items(),
            'meta' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ], $statusCode);
    }

    protected function error(string $message = 'Error', int $statusCode = Response::HTTP_BAD_REQUEST, mixed $errors = null, ?string $code = null): JsonResponse
    {
        if ($code === null) {
            $code = match ($statusCode) {
                Response::HTTP_UNAUTHORIZED => 'UNAUTHENTICATED',
                Response::HTTP_FORBIDDEN => 'FORBIDDEN',
                Response::HTTP_NOT_FOUND => 'NOT_FOUND',
                Response::HTTP_CONFLICT => 'BUSINESS_CONFLICT',
                Response::HTTP_UNPROCESSABLE_ENTITY => 'VALIDATION_ERROR',
                Response::HTTP_TOO_MANY_REQUESTS => 'TOO_MANY_REQUESTS',
                Response::HTTP_INTERNAL_SERVER_ERROR => 'SERVER_ERROR',
                default => 'ERROR',
            };
        }

        $payload = [
            'success' => false,
            'code' => $code,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $statusCode);
    }

    protected function authorizeRoles(string ...$roles): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(response()->json([
                'success' => false,
                'code' => 'UNAUTHENTICATED',
                'message' => 'Missing, invalid or expired JWT',
            ], Response::HTTP_UNAUTHORIZED));
        }

        if (! in_array($user->role, $roles, true)) {
            abort(response()->json([
                'success' => false,
                'code' => 'FORBIDDEN',
                'message' => 'Role not allowed',
            ], Response::HTTP_FORBIDDEN));
        }
    }

    public static function roleMiddleware(string ...$roles): \Closure
    {
        return function ($request, $next) use ($roles) {
            $user = $request->user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'code' => 'UNAUTHENTICATED',
                    'message' => 'Missing, invalid or expired JWT',
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (! in_array($user->role, $roles, true)) {
                return response()->json([
                    'success' => false,
                    'code' => 'FORBIDDEN',
                    'message' => 'Role not allowed',
                ], Response::HTTP_FORBIDDEN);
            }

            return $next($request);
        };
    }
}

