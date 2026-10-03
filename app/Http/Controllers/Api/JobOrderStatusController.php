<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrder\ChangeStatusRequest;
use App\Http\Requests\JobOrder\CorrectStatusRequest;
use App\Http\Resources\JobOrderResource;
use App\Models\JobOrder;
use App\Models\JobOrderStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class JobOrderStatusController extends Controller
{
    protected array $allowedTransitions = [
        JobOrder::STATUS_PENDING => [
            JobOrder::STATUS_IN_PROGRESS,
            JobOrder::STATUS_CANCELLED,
        ],
        JobOrder::STATUS_IN_PROGRESS => [
            JobOrder::STATUS_WAITING_PARTS,
            JobOrder::STATUS_READY,
            JobOrder::STATUS_CANCELLED,
        ],
        JobOrder::STATUS_WAITING_PARTS => [
            JobOrder::STATUS_IN_PROGRESS,
            JobOrder::STATUS_CANCELLED,
        ],
        JobOrder::STATUS_READY => [
            JobOrder::STATUS_DELIVERED,
            JobOrder::STATUS_CANCELLED,
        ],
        JobOrder::STATUS_DELIVERED => [],
        JobOrder::STATUS_CANCELLED => [],
    ];

    protected array $statusWeights = [
        JobOrder::STATUS_PENDING => 1,
        JobOrder::STATUS_WAITING_PARTS => 2,
        JobOrder::STATUS_IN_PROGRESS => 2,
        JobOrder::STATUS_READY => 3,
        JobOrder::STATUS_DELIVERED => 4,
    ];

    public function update(ChangeStatusRequest $request, JobOrder $jobOrder): JsonResponse
    {
        $currentUser = auth()->user();
        $fromStatus = $jobOrder->status;
        $toStatus = $request->input('status');

        if ($currentUser->role === 'technician') {
            if ($jobOrder->assigned_technician_id !== $currentUser->id) {
                return $this->error('غير مصرح لك بتغيير حالة أمر شغل غير مسند إليك', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
            }
        }

        $allowedNext = $this->allowedTransitions[$fromStatus] ?? [];
        if (!in_array($toStatus, $allowedNext, true)) {
            return $this->error(
                "الانتقال من الحالة '{$fromStatus}' إلى الحالة '{$toStatus}' غير مسموح في مسار العمل",
                Response::HTTP_CONFLICT,
                null,
                'INVALID_STATUS_TRANSITION'
            );
        }

        if ($toStatus === JobOrder::STATUS_CANCELLED) {
            if (Schema::hasTable('invoices')) {
                $hasPaidInvoice = DB::table('invoices')
                    ->where('job_order_id', $jobOrder->id)
                    ->where('payment_status', 'paid')
                    ->exists();

                if ($hasPaidInvoice) {
                    return $this->error(
                        'لا يمكن إلغاء أمر شغل مسجل له دفعات مالية مسددة مسبقاً',
                        Response::HTTP_CONFLICT,
                        null,
                        'ALREADY_PAID'
                    );
                }
            }

            if (Schema::hasTable('job_order_parts') && Schema::hasTable('inventory_items')) {
                DB::transaction(function () use ($jobOrder) {
                    $parts = DB::table('job_order_parts')->where('job_order_id', $jobOrder->id)->get();
                    foreach ($parts as $part) {
                        DB::table('inventory_items')->where('id', $part->inventory_item_id)->increment('quantity', $part->quantity_used);
                    }
                });
            }
        }

        DB::transaction(function () use ($jobOrder, $fromStatus, $toStatus, $currentUser, $request) {
            JobOrderStatusHistory::create([
                'job_order_id' => $jobOrder->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'changed_by_user_id' => $currentUser->id,
                'is_correction' => false,
                'note' => $request->input('note'),
            ]);

            $jobOrder->update(['status' => $toStatus]);
        });

        $jobOrder->load(['vehicle.customer', 'assignedTechnician', 'media']);

        return $this->success(
            new JobOrderResource($jobOrder),
            'تم تحديث حالة أمر الشغل بنجاح'
        );
    }

    public function correct(CorrectStatusRequest $request, JobOrder $jobOrder): JsonResponse
    {
        $currentUser = auth()->user();

        if ($currentUser->role !== 'owner') {
            return $this->error('التصحيح العكسي للحالات متاح فقط للمالك (Owner only)', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        $fromStatus = $jobOrder->status;
        $toStatus = $request->input('to_status');

        if ($fromStatus === JobOrder::STATUS_CANCELLED) {
            return $this->error(
                'لا يمكن تصحيح أمر ملغي مسبقاً (حالة نهائية)',
                Response::HTTP_CONFLICT,
                null,
                'CANNOT_CORRECT_CANCELLED_ORDER'
            );
        }

        $currentWeight = $this->statusWeights[$fromStatus] ?? 0;
        $targetWeight = $this->statusWeights[$toStatus] ?? 0;

        if ($targetWeight >= $currentWeight) {
            return $this->error(
                'التصحيح مسموح به للرجوع لحالة سابقة فقط',
                Response::HTTP_CONFLICT,
                null,
                'BACKWARD_CORRECTION_ONLY'
            );
        }

        DB::transaction(function () use ($jobOrder, $fromStatus, $toStatus, $currentUser, $request) {
            JobOrderStatusHistory::create([
                'job_order_id' => $jobOrder->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'changed_by_user_id' => $currentUser->id,
                'is_correction' => true,
                'note' => $request->input('note'),
            ]);

            $jobOrder->update(['status' => $toStatus]);
        });

        $jobOrder->load(['vehicle.customer', 'assignedTechnician', 'media']);

        return $this->success(
            new JobOrderResource($jobOrder),
            'تم تصحيح حالة أمر الشغل بنجاح'
        );
    }
}
