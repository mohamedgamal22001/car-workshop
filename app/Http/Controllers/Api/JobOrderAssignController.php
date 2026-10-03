<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrder\AssignTechnicianRequest;
use App\Http\Resources\JobOrderResource;
use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JobOrderAssignController extends Controller
{
    public function __invoke(AssignTechnicianRequest $request, JobOrder $jobOrder): JsonResponse
    {
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['owner', 'front_desk'], true)) {
            return $this->error('غير مصرح لك بتعيين الفنيين', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        $technicianId = $request->input('technician_id');

        $technician = User::where('workshop_id', $currentUser->workshop_id)
            ->where('role', 'technician')
            ->find($technicianId);

        if (!$technician) {
            return $this->error(
                'الفني المختار غير صحيح أو لا يتبع هذه الورشة',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ['technician_id' => ['الفني المختار غير صحيح أو غير نشط']],
                'VALIDATION_ERROR'
            );
        }

        $jobOrder->assigned_technician_id = $technician->id;
        $jobOrder->save();

        $jobOrder->load(['vehicle.customer', 'assignedTechnician', 'media']);

        return $this->success(
            new JobOrderResource($jobOrder),
            'تم إسناد الفني لأمر الشغل بنجاح'
        );
    }
}
