<?php

namespace App\Http\Requests\Mobile\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
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
            'email' => [
                'required',
                'exists:users,email',
                Rule::email()
                // ->rfcCompliant(strict: false)
                // ->validateMxRecord()
                // ->preventSpoofing()
            ],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'fcm_token' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'email_is_required',
            'email.email' => 'invalid_email_format',
            'email.exists' => 'email_not_found',
            'password.min' => 'password_minimum_8_characters',
            'password.letters' => 'password_must_contain_letters',
            'password.mixed' => 'password_must_contain_upper_and_lowercase_letters',
            'password.numbers' => 'password_must_contain_numbers',
            'password.symbols' => 'password_must_contain_symbols',
            'fcm_token.required' => 'fcm_token_is_required',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'example' => 'user1@deepgrow.io',
            ],
            'password' => [
                'example' => '12345678Aa#',
            ],
            'fcm_token' => [
                'example' => 'abc123xyz',
            ],
        ];
    }
}
