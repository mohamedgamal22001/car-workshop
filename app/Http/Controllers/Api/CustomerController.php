<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SearchCustomerRequest;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\VehicleResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            self::roleMiddleware('owner', 'front_desk'),
        ];
    }

    public function search(SearchCustomerRequest $request): JsonResponse
    {
        $phone = $request->query('phone');

        $customers = Customer::with('vehicles')
            ->where('phone', 'like', "%{$phone}%")
            ->get();

        return $this->success(
            CustomerResource::collection($customers),
            'تم استرجاع بيانات العملاء بنجاح'
        );
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 20);
        $perPage = min(max(1, $perPage), 100);

        $query = Customer::with('vehicles');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $paginator = $query->latest()->paginate($perPage);

        return $this->paginated(
            $paginator,
            CustomerResource::collection($paginator->items()),
            'تم جلب قائمة العملاء بنجاح'
        );
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return $this->success(
            new CustomerResource($customer),
            'تم إنشاء العميل بنجاح',
            Response::HTTP_CREATED
        );
    }

    public function show(Customer $customer): JsonResponse
    {
        $customer->load('vehicles');

        return $this->success(
            new CustomerResource($customer),
            'تم جلب تفاصيل العميل'
        );
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        return $this->success(
            new CustomerResource($customer->fresh('vehicles')),
            'تم تعديل بيانات العميل بنجاح'
        );
    }

    public function vehicles(Customer $customer): JsonResponse
    {
        $vehicles = $customer->vehicles()->latest()->get();

        return $this->success(
            VehicleResource::collection($vehicles),
            'تم جلب مركبات العميل بنجاح'
        );
    }
}
