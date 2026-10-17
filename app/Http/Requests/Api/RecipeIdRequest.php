<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RecipeIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_id' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipe_id.required' => 'The recipe_id field is required',
            'recipe_id.integer' => 'The recipe_id must be an integer',
        ];
    }
}
