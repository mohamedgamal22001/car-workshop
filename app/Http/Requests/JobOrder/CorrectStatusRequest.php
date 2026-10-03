<?php

namespace App\Http\Requests\JobOrder;

use App\Http\Requests\BaseRequest;

class CorrectStatusRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Document 3 Section 3.7 & 6: Body: to_status, note (reason for backward correction).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'to_status' => 'required|string|in:pending,in_progress,waiting_parts,ready',
            'note' => 'required|string|max:1000',
        ];
    }
}
