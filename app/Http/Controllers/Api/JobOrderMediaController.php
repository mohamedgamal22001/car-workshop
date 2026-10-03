<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOrder\ListJobOrderMediaRequest;
use App\Http\Requests\JobOrder\StoreJobOrderMediaRequest;
use App\Http\Resources\JobOrderMediaResource;
use App\Models\JobOrder;
use App\Models\JobOrderMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class JobOrderMediaController extends Controller
{
    public function index(ListJobOrderMediaRequest $request, JobOrder $jobOrder): JsonResponse
    {
        $query = $jobOrder->media();

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        $media = $query->latest()->get();

        return $this->success(
            JobOrderMediaResource::collection($media),
            'تم استرجاع وسائط أمر الشغل بنجاح'
        );
    }

    public function store(StoreJobOrderMediaRequest $request, JobOrder $jobOrder): JsonResponse
    {
        $file = $request->file('file');
        $type = $request->input('type');

        $path = $file->store("job_orders/{$jobOrder->id}", 'public');

        $media = JobOrderMedia::create([
            'job_order_id' => $jobOrder->id,
            'type' => $type,
            'path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return $this->success(
            new JobOrderMediaResource($media),
            'تم رفع الصورة بنجاح',
            Response::HTTP_CREATED
        );
    }

    public function destroy(JobOrderMedia $media): JsonResponse
    {
        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return $this->success(null, 'تم حذف الصورة بنجاح');
    }
}
