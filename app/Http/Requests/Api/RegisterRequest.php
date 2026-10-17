<?php

namespace App\Http\Requests\Api;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', Rule::unique('users', 'email')],
            'password' => [
                'required',
                'string',
                'min:5',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (!is_string($value)) {
                        return;
                    }

                    if (!preg_match('/^[A-Za-z0-9#_\-$]+$/', $value)) {
                        $fail('The password may contain only latin letters, digits and symbols # _ - $');
                    }

                    if (!preg_match('/[a-z]/', $value)) {
                        $fail('The password must contain at least one lowercase letter');
                    }

                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('The password must contain at least one uppercase letter');
                    }

                    if (!preg_match('/[#_\-$]/', $value)) {
                        $fail('The password must contain at least one special character (# _ - $)');
                    }
                },
            ],
            'fullname' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The email field is required',
            'email.email' => 'The email must be a valid email address',
            'email.unique' => 'The email has already been taken',
            'password.required' => 'The password field is required',
            'password.min' => 'The password must be at least 5 characters',
        ];
    }
}
