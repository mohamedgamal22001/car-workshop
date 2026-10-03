<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UpdateUserRequest extends BaseRequest
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
            'email' => 'sometimes|nullable|email|max:255',
            'phone' => 'sometimes|required|string|max:50',
            'password' => 'sometimes|nullable|string|min:6',
            'role' => 'sometimes|required|string|in:owner,front_desk,technician',
            'is_active' => 'sometimes|required|boolean',
        ];
    }
}
