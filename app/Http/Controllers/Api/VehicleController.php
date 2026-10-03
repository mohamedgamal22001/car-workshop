<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\SearchVehicleRequest;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Symfony\Component\HttpFoundation\Response;

class VehicleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            self::roleMiddleware('owner', 'front_desk'),
        ];
    }

    public function search(SearchVehicleRequest $request): JsonResponse
    {
        $plate = $request->query('plate');

        $vehicle = Vehicle::with('customer')
            ->where('plate_number', $plate)
            ->first();

        if (!$vehicle) {
            return $this->success(null, 'لا توجد مركبة سابقة مسجلة بهذا الرقم في هذه الورشة');
        }

        return $this->success(
            new VehicleResource($vehicle),
            'تنبيه: توجد مركبة سابقة مسجلة بنفس رقم اللوحة في هذه الورشة'
        );
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $data = $request->validated();

        $customer = Customer::find($data['customer_id']);
        if (!$customer) {
            return $this->error(
                'العميل المختار غير موجود في هذه الورشة',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ['customer_id' => ['العميل المختار غير صحيح']],
                'VALIDATION_ERROR'
            );
        }

        $vehicle = Vehicle::create($data);

        return $this->success(
            new VehicleResource($vehicle),
            'تمت إضافة المركبة بنجاح',
            Response::HTTP_CREATED
        );
    }

    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load('customer');

        return $this->success(
            new VehicleResource($vehicle),
            'تم استرجاع تفاصيل المركبة بنجاح'
        );
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        $vehicle->update($request->validated());

        return $this->success(
            new VehicleResource($vehicle->fresh('customer')),
            'تم تعديل بيانات المركبة بنجاح'
        );
    }
}
