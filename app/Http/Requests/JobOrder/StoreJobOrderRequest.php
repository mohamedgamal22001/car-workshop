<?php

namespace App\Http\Requests\JobOrder;

use App\Http\Requests\BaseRequest;

class StoreJobOrderRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'description' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date',
            'assigned_technician_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
