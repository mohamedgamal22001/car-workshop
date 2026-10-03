<?php

namespace App\Http\Requests\JobOrder;

use App\Http\Requests\BaseRequest;

class ListJobOrdersRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Document 3 Section 6: filters (status, overdue, technician_id, customer_id, vehicle_id).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:pending,in_progress,waiting_parts,ready,delivered,cancelled',
            'overdue' => 'nullable|in:true,false,1,0',
            'technician_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'vehicle_id' => 'nullable|integer',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
