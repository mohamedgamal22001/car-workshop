<?php

namespace App\Http\Requests\Workshop;

use App\Http\Requests\BaseRequest;

class UpdateWorkshopRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'owner_phone' => 'sometimes|required|string|max:50',
        ];
    }
}
