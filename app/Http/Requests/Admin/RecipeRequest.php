<?php

namespace App\Http\Requests\Admin;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ingredients = array_values(array_filter($this->input('ingredients', []), function ($row) {
            return !empty($row['id']) || !empty($row['quantity']);
        }));

        $data = [
            'ingredients' => $ingredients,
            'price' => $this->price === '' || $this->price === null ? 0 : $this->price,
        ];

        if ($this->isMethod('post')) {
            $data['steps'] = array_values(array_filter($this->input('steps', []), function ($row) {
                return !empty($row['title']) || !empty($row['description']);
            }));
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        $rules = [
            'title' => ['required', 'max:20'],
            'description' => ['required', 'min:50', 'max:250'],
            'cooking_time' => ['required', 'integer', 'min:1'],
            'difficulty' => ['required', Rule::in(['легко', 'средне', 'сложно'])],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'price' => [
                'nullable',
                'regex:/^\d+(\.\d{1,2})?$/',
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($value > 0 && $value <= 100) {
                        $fail('Цена должна быть больше 100 или 0');
                    }
                },
            ],

            'images' => [$isCreate ? 'required' : 'nullable', 'array', 'min:1', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=500,min_height=500'],
            'default_image' => ['nullable', 'integer', 'min:1'],
            'default_image_id' => ['nullable', Rule::exists('recipe_images', 'id')],

            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.id' => ['required', 'distinct', Rule::exists('ingredients', 'id')],
            'ingredients.*.quantity' => ['required', 'numeric', 'gt:0'],
        ];

        if ($isCreate) {
            $rules['steps'] = ['required', 'array', 'min:1'];
            $rules['steps.*.title'] = ['required', 'max:40'];
            $rules['steps.*.description'] = ['required', 'max:255'];
            $rules['steps.*.image'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Введите название рецепта',
            'title.max' => 'Название не больше 20 символов',
            'description.required' => 'Введите описание',
            'description.min' => 'Описание не меньше 50 символов',
            'description.max' => 'Описание не больше 250 символов',
            'cooking_time.required' => 'Введите время приготовления',
            'cooking_time.integer' => 'Время должно быть числом',
            'cooking_time.min' => 'Время должно быть больше 0',
            'difficulty.required' => 'Выберите сложность',
            'difficulty.in' => 'Неверная сложность',
            'category_id.required' => 'Выберите категорию',
            'category_id.exists' => 'Категория не найдена',
            'price.regex' => 'Цена в формате хх.хх',

            'images.required' => 'Добавьте хотя бы одно фото',
            'images.min' => 'Добавьте хотя бы одно фото',
            'images.max' => 'Не больше 5 фото',
            'images.*.image' => 'Файл должен быть изображением',
            'images.*.mimes' => 'Допустимые форматы: jpg, jpeg, png, webp',
            'images.*.max' => 'Размер фото не больше 2 Мб',
            'images.*.dimensions' => 'Фото должно быть не меньше 500px по каждой стороне',

            'ingredients.required' => 'Добавьте хотя бы один ингредиент',
            'ingredients.min' => 'Добавьте хотя бы один ингредиент',
            'ingredients.*.id.required' => 'Выберите ингредиент',
            'ingredients.*.id.distinct' => 'Ингредиент повторяется',
            'ingredients.*.id.exists' => 'Ингредиент не найден',
            'ingredients.*.quantity.required' => 'Введите количество',
            'ingredients.*.quantity.numeric' => 'Количество должно быть числом',
            'ingredients.*.quantity.gt' => 'Количество должно быть больше 0',

            'steps.required' => 'Добавьте хотя бы один шаг',
            'steps.min' => 'Добавьте хотя бы один шаг',
            'steps.*.title.required' => 'Введите название шага',
            'steps.*.title.max' => 'Название шага не больше 40 символов',
            'steps.*.description.required' => 'Введите описание шага',
            'steps.*.description.max' => 'Описание шага не больше 255 символов',
            'steps.*.image.required' => 'Добавьте фото шага',
            'steps.*.image.image' => 'Файл должен быть изображением',
            'steps.*.image.mimes' => 'Допустимые форматы: jpg, jpeg, png, webp',
            'steps.*.image.max' => 'Размер фото не больше 2 Мб',
        ];
    }
}
