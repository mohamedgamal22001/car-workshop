<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOrderStatusHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Document 3 Section 3.7: Job_Order_Status_History audit log.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_order_id' => $this->job_order_id,
            'from_status' => $this->from_status,
            'to_status' => $this->to_status,
            'changed_by_user_id' => $this->changed_by_user_id,
            'changed_by' => new UserResource($this->whenLoaded('changedByUser')),
            'is_correction' => (bool)$this->is_correction,
            'note' => $this->note,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
