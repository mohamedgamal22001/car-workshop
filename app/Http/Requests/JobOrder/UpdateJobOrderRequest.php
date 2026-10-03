<?php

namespace App\Http\Requests\JobOrder;

use App\Http\Requests\BaseRequest;

class UpdateJobOrderRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Document 3 Section 5: Edit description, estimated_price, labor_cost, expected_delivery_date.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'sometimes|nullable|string',
            'estimated_price' => 'sometimes|nullable|numeric|min:0',
            'labor_cost' => 'sometimes|nullable|numeric|min:0',
            'expected_delivery_date' => 'sometimes|nullable|date',
        ];
    }
}
