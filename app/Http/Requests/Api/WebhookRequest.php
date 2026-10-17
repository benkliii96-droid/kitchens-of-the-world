<?php

namespace App\Http\Requests\Api;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_id' => ['required', 'string'],
            'status' => ['required', Rule::in([Purchase::STATUS_PAID, Purchase::STATUS_FAILED])],
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_id.required' => 'The purchase_id field is required',
            'status.required' => 'The status field is required',
            'status.in' => 'The status must be paid or failed',
        ];
    }
}
