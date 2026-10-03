<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\BaseRequest;

class SearchVehicleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plate' => 'required|string|max:50',
        ];
    }
}
