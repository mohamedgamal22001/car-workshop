<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workshop\UpdateWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Symfony\Component\HttpFoundation\Response;

class WorkshopController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            self::roleMiddleware('owner'),
        ];
    }

    public function show(Workshop $workshop): JsonResponse
    {
        $currentUser = auth()->user();

        if ($workshop->id !== $currentUser->workshop_id) {
            return $this->error('Record missing or belongs to another workshop', Response::HTTP_NOT_FOUND, null, 'NOT_FOUND');
        }

        return $this->success($workshop, 'Workshop details retrieved successfully');
    }

    public function update(UpdateWorkshopRequest $request, Workshop $workshop): JsonResponse
    {
        $currentUser = auth()->user();

        if ($workshop->id !== $currentUser->workshop_id) {
            return $this->error('Record missing or belongs to another workshop', Response::HTTP_NOT_FOUND, null, 'NOT_FOUND');
        }

        $workshop->update($request->validated());

        return $this->success($workshop, 'Workshop updated successfully');
    }
}
