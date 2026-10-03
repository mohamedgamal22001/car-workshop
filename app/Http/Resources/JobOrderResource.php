<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Document 3 Section 2.2 & 5: Scoped by workshop_id; no price fields for technicians.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isTechnician = $user && $user->role === 'technician';

        return [
            'id' => $this->id,
            'workshop_id' => $this->workshop_id,
            'vehicle_id' => $this->vehicle_id,
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'assigned_technician_id' => $this->assigned_technician_id,
            'assigned_technician' => new TechnicianResource($this->whenLoaded('assignedTechnician')),
            'description' => $this->description,
            'status' => $this->status,

            // إخفاء حقول الأسعار تماماً عن الفني طبقاً لمستند المتطلبات (Document 3 Section 2.2 & 5)
            'estimated_price' => $this->when(!$isTechnician, $this->estimated_price),
            'labor_cost' => $this->when(!$isTechnician, $this->labor_cost),

            'expected_delivery_date' => $this->expected_delivery_date?->toIso8601String(),
            'is_overdue' => (bool)$this->is_overdue,
            'media' => JobOrderMediaResource::collection($this->whenLoaded('media')),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
