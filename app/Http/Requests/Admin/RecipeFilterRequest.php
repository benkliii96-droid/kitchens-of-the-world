<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecipeFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'difficulty' => ['nullable', Rule::in(['легко', 'средне', 'сложно'])],
            'time' => ['nullable', Rule::in(['to15', '60-120', 'from120'])],
            'category_id' => ['nullable', 'integer'],
            'price' => ['nullable', Rule::in(['free', 'paid'])],
            'sort' => ['nullable', Rule::in(['newest_desc', 'newest_asc', 'time_asc', 'time_desc'])],
        ];
    }
}
