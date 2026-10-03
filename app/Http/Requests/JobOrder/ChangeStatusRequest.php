<?php

namespace App\Http\Requests\JobOrder;

use App\Http\Requests\BaseRequest;

class ChangeStatusRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Document 3 Section 6: Status change following transition table.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,in_progress,waiting_parts,ready,delivered,cancelled',
            'note' => 'nullable|string|max:1000',
        ];
    }
}
