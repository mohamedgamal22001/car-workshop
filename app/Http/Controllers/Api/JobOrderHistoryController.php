<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobOrderStatusHistoryResource;
use App\Models\JobOrder;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JobOrderHistoryController extends Controller
{
    public function index(JobOrder $jobOrder): JsonResponse
    {
        $currentUser = auth()->user();

        if ($currentUser->role === 'technician' && $jobOrder->assigned_technician_id !== $currentUser->id) {
            return $this->error('غير مصرح لك بالاطلاع على سجل أمر شغل غير مسند إليك', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        $histories = $jobOrder->statusHistories()
            ->with('changedByUser')
            ->latest()
            ->get();

        return $this->success(
            JobOrderStatusHistoryResource::collection($histories),
            'تم استرجاع سجل حالات أمر الشغل بنجاح'
        );
    }
}
