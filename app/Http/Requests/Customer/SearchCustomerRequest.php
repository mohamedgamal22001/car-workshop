<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\BaseRequest;

class SearchCustomerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:2|max:50',
        ];
    }
}
