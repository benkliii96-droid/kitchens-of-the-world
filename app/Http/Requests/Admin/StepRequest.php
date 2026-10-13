<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRules = ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];

        if ($this->isMethod('post')) {
            array_unshift($imageRules, 'required');
        } else {
            array_unshift($imageRules, 'nullable');
        }

        return [
            'recipe_id' => ['required', Rule::exists('recipes', 'id')],
            'title' => ['required', 'max:40'],
            'description' => ['required', 'max:255'],
            'image' => $imageRules,
        ];
    }

    public function messages(): array
    {
        return [
            'recipe_id.required' => 'Выберите рецепт',
            'recipe_id.exists' => 'Рецепт не найден',
            'title.required' => 'Введите название шага',
            'title.max' => 'Название не больше 40 символов',
            'description.required' => 'Введите описание шага',
            'description.max' => 'Описание не больше 255 символов',
            'image.required' => 'Добавьте фотографию шага',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpg, jpeg, png, webp',
            'image.max' => 'Размер файла не больше 2 Мб',
        ];
    }
}
