<?php

namespace App\Http\Requests\Mobile\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'reset_token' => ['required', 'string', 'size:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'password_is_required',
            'password.min' => 'password_minimum_8_characters',
            'password.letters' => 'password_must_contain_letters',
            'password.mixed' => 'password_must_contain_upper_and_lowercase_letters',
            'password.numbers' => 'password_must_contain_numbers',
            'password.symbols' => 'password_must_contain_symbols',
            'reset_token.required' => 'reset_token_is_required',
            'reset_token.string' => 'reset_token_must_be_string',
            'reset_token.size' => 'reset_token_must_be_64_characters',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'password' => [
                'example' => '12345678Aa#',
            ],
            'reset_token' => [
                'description' => 'Reset token for password reset.',
                'example' => 'abcdef1234567890abcdef1234567890abcdef1234567890',
            ],
        ];
    }
}
