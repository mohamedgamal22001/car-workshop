<?php

namespace App\Http\Requests\JobOrder;

use App\Http\Requests\BaseRequest;

class ListJobOrderMediaRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Document 3 Section 5: List media, optional ?type= (before_work / after_work).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'nullable|string|in:before_work,after_work',
        ];
    }
}
