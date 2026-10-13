<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'max:20',
                Rule::unique('ingredients', 'title')->ignore($this->route('ingredient')),
            ],
            'unit' => [
                'required',
                Rule::in(['шт', 'л', 'кг', 'мг', 'ст.л.']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Введите название ингредиента',
            'title.max' => 'Название не больше 20 символов',
            'title.unique' => 'Такой ингредиент уже есть',
            'unit.required' => 'Выберите единицу измерения',
            'unit.in' => 'Неверная единица измерения',
        ];
    }
}
