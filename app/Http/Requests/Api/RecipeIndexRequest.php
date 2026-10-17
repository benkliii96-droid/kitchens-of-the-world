<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecipeIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'difficulty' => ['nullable', Rule::in(['легко', 'средне', 'сложно'])],
            'time_min' => ['nullable', 'integer', 'min:0'],
            'time_max' => ['nullable', 'integer', 'min:0'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', Rule::in(['newest_desc', 'newest_asc', 'time_asc', 'time_desc'])],
        ];
    }
}
