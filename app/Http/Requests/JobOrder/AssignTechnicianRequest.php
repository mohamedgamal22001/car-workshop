<?php

namespace App\Http\Requests\JobOrder;

use App\Http\Requests\BaseRequest;

class AssignTechnicianRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Document 3 Section 6: Assign / reassign technician.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'technician_id' => 'required|integer|exists:users,id',
        ];
    }
}
