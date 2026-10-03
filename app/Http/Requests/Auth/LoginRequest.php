<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required_without_all:email,phone|nullable|string',
            'email' => 'required_without_all:login,phone|nullable|string',
            'phone' => 'required_without_all:login,email|nullable|string',
            'password' => 'required|string',
        ];
    }
}
