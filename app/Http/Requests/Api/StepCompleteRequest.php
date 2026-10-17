<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StepCompleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_id' => ['required', 'integer'],
            'completed' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_id.required' => 'The purchase_id field is required',
            'purchase_id.integer' => 'The purchase_id must be an integer',
            'completed.required' => 'The completed field is required',
            'completed.boolean' => 'The completed field must be true or false',
        ];
    }
}
