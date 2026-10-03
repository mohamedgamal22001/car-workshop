<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrder\ListJobOrdersRequest;
use App\Http\Requests\JobOrder\StoreJobOrderRequest;
use App\Http\Requests\JobOrder\UpdateJobOrderRequest;
use App\Http\Resources\JobOrderResource;
use App\Models\JobOrder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class JobOrderController extends Controller
{
    public function index(ListJobOrdersRequest $request): JsonResponse
    {
        $currentUser = auth()->user();
        $query = JobOrder::with(['vehicle.customer', 'assignedTechnician', 'media']);

        if ($currentUser->role === 'technician') {
            $query->where('assigned_technician_id', $currentUser->id);
        } elseif ($technicianId = $request->query('technician_id')) {
            $query->where('assigned_technician_id', $technicianId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if (in_array($request->query('overdue'), ['true', '1'], true)) {
            $query->where('expected_delivery_date', '<', now())
                  ->whereNotIn('status', ['delivered', 'cancelled']);
        }

        if ($customerId = $request->query('customer_id')) {
            $query->whereHas('vehicle', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            });
        }

        if ($vehicleId = $request->query('vehicle_id')) {
            $query->where('vehicle_id', $vehicleId);
        }

        $perPage = (int)$request->query('per_page', 20);
        $paginator = $query->latest()->paginate(min(max(1, $perPage), 100));

        return $this->paginated(
            $paginator,
            JobOrderResource::collection($paginator->items()),
            'تم استرجاع قائمة أوامر الشغل بنجاح'
        );
    }

    public function store(StoreJobOrderRequest $request): JsonResponse
    {
        $currentUser = auth()->user();
        $workshopId = $currentUser->workshop_id;

        $idempotencyKey = $request->header('Idempotency-Key');
        $cacheKey = $idempotencyKey ? "idempotency:workshop_{$workshopId}:{$idempotencyKey}" : null;

        if ($cacheKey && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            return response()->json($cached, Response::HTTP_OK);
        }

        $data = $request->validated();

        $vehicle = Vehicle::find($data['vehicle_id']);
        if (!$vehicle) {
            return $this->error(
                'المركبة المختارة غير مسجلة في هذه الورشة',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ['vehicle_id' => ['مركبة غير صحيحة']],
                'VALIDATION_ERROR'
            );
        }

        if (!empty($data['assigned_technician_id'])) {
            $technician = User::where('workshop_id', $workshopId)
                ->where('role', 'technician')
                ->find($data['assigned_technician_id']);

            if (!$technician) {
                return $this->error(
                    'الفني المختار غير صحيح أو لا يتبع هذه الورشة',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    ['assigned_technician_id' => ['فني غير صحيح']],
                    'VALIDATION_ERROR'
                );
            }
        }

        $data['status'] = JobOrder::STATUS_PENDING;
        $data['created_by'] = $currentUser->id;

        $jobOrder = JobOrder::create($data);
        $jobOrder->load(['vehicle.customer', 'assignedTechnician']);

        $responseData = [
            'success' => true,
            'message' => 'تم إنشاء أمر الشغل بنجاح',
            'data' => (new JobOrderResource($jobOrder))->resolve($request),
        ];

        if ($cacheKey) {
            Cache::put($cacheKey, $responseData, now()->addDay());
        }

        return response()->json($responseData, Response::HTTP_CREATED);
    }

    public function show(JobOrder $jobOrder): JsonResponse
    {
        $currentUser = auth()->user();

        if ($currentUser->role === 'technician' && $jobOrder->assigned_technician_id !== $currentUser->id) {
            return $this->error('غير مصرح لك بالاطلاع على أمر شغل غير مسند إليك', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        $jobOrder->load(['vehicle.customer', 'assignedTechnician', 'media']);

        return $this->success(
            new JobOrderResource($jobOrder),
            'تم استرجاع تفاصيل أمر الشغل بنجاح'
        );
    }

    public function update(UpdateJobOrderRequest $request, JobOrder $jobOrder): JsonResponse
    {
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['owner', 'front_desk'], true)) {
            return $this->error('غير مصرح لك بتعديل بيانات أمر الشغل', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        $data = $request->validated();

        if (array_key_exists('labor_cost', $data) && Schema::hasTable('invoices')) {
            $isInvoiceLocked = DB::table('invoices')
                ->where('job_order_id', $jobOrder->id)
                ->whereNotNull('locked_at')
                ->exists();

            if ($isInvoiceLocked) {
                return $this->error(
                    'لا يمكن تعديل المصنعية لأن فاتورة هذا الأمر تم قفلها مسبقاً',
                    Response::HTTP_CONFLICT,
                    null,
                    'INVOICE_LOCKED'
                );
            }
        }

        $jobOrder->update($data);
        $jobOrder->load(['vehicle.customer', 'assignedTechnician', 'media']);

        return $this->success(
            new JobOrderResource($jobOrder),
            'تم تحديث أمر الشغل بنجاح'
        );
    }

    public function destroy(JobOrder $jobOrder): JsonResponse
    {
        return $this->error('الحذف غير متاح، يرجى تغيير حالة الأمر إلى cancelled', Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
