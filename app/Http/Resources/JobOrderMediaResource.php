<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class JobOrderMediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Document 3 Section 3.6: Job_Order_Media fields.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_order_id' => $this->job_order_id,
            'type' => $this->type,
            'path' => $this->path,
            'url' => Storage::url($this->path),
            'uploaded_by' => $this->uploaded_by,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
