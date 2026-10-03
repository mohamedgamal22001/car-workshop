<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\TechnicianResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $currentUser = auth()->user();
        $userRole = $currentUser->role;

        if ($request->query('role') === 'technician') {
            if (!in_array($userRole, ['owner', 'front_desk'], true)) {
                return $this->error('غير مصرح لك بهذا الإجراء', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
            }

            $technicians = User::where('workshop_id', $currentUser->workshop_id)
                ->where('role', 'technician')
                ->select(['id', 'name', 'is_active'])
                ->orderBy('name')
                ->get();

            return $this->success(
                TechnicianResource::collection($technicians),
                'تم جلب قائمة الفنيين بنجاح'
            );
        }

        if ($userRole !== 'owner') {
            return $this->error('غير مصرح لك بالاطلاع على جميع المستخدمين', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        $query = User::where('workshop_id', $currentUser->workshop_id);

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        $users = $query->latest()->get();

        return $this->success(
            UserResource::collection($users),
            'تم جلب قائمة المستخدمين بنجاح'
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $currentUser = auth()->user();
        $userRole = is_object($currentUser->role) ? $currentUser->role->value : $currentUser->role;

        if ($userRole !== 'owner') {
            return $this->error('غير مصرح لك بإضافة مستخدمين', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        $data = $request->validated();
        $data['workshop_id'] = $currentUser->workshop_id;

        $user = User::create($data);

        return $this->success(
            new UserResource($user),
            'تم إنشاء المستخدم بنجاح',
            Response::HTTP_CREATED
        );
    }

    public function show(User $user): JsonResponse
    {
        $currentUser = auth()->user();
        $userRole = is_object($currentUser->role) ? $currentUser->role->value : $currentUser->role;

        if ($userRole !== 'owner' && $currentUser->id !== $user->id) {
            return $this->error('غير مصرح لك بالوصول لهذا الحساب', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        if ($user->workshop_id !== $currentUser->workshop_id) {
            return $this->error('المستخدم غير موجود في هذه الورشة', Response::HTTP_NOT_FOUND, null, 'NOT_FOUND');
        }

        return $this->success(
            new UserResource($user),
            'تم جلب تفاصيل المستخدم بنجاح'
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $currentUser = auth()->user();
        $userRole = is_object($currentUser->role) ? $currentUser->role->value : $currentUser->role;

        if ($userRole !== 'owner') {
            return $this->error('غير مصرح لك بتعديل بيانات المستخدمين', Response::HTTP_FORBIDDEN, null, 'FORBIDDEN');
        }

        if ($user->workshop_id !== $currentUser->workshop_id) {
            return $this->error('المستخدم غير موجود في هذه الورشة', Response::HTTP_NOT_FOUND, null, 'NOT_FOUND');
        }

        $user->update($request->validated());

        return $this->success(
            new UserResource($user->fresh()),
            'تم تعديل بيانات المستخدم بنجاح'
        );
    }
}
