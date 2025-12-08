<?php

namespace App\Http\Requests\Mobile\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                Rule::email()
                // ->rfcCompliant(strict: false)
                // ->validateMxRecord()
                // ->preventSpoofing()
            ],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'fcm_token' => 'required|string'
        ];

        $provider = $this->provider ?? null;
        if ($provider) {
            $rules['provider'] = ['string', Rule::in(['google', 'apple', 'facebook'])];
            $rules['provider_id'] = 'required|string';
            $rules['password'] = 'nullable';
            // Remove unique email check if provider is present
        } else {
            // Add unique email check only if provider is not present
            $rules['email'][] = Rule::unique('users', 'email')
                ->where(fn($q) => $q->where('status', 'active'));
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'full_name_is_required',
            'full_name.string' => 'full_name_must_be_a_string',
            'full_name.max' => 'full_name_must_not_exceed_255_characters',
            'email.required' => 'email_is_required',
            'email.unique' => 'email_already_exists',
            'email.email' => 'invalid_email_format',
            'password.min' => 'password_minimum_8_characters',
            'password.letters' => 'password_must_contain_letters',
            'password.mixed' => 'password_must_contain_upper_and_lowercase_letters',
            'password.numbers' => 'password_must_contain_numbers',
            'password.symbols' => 'password_must_contain_symbols',
            'fcm_token.required' => 'fcm_token_is_required',
            'fcm_token.string' => 'fcm_token_must_be_a_string',
            'provider.string' => 'provider_must_be_a_string',
            'provider.in' => 'provider_must_be_one_of_google_apple_facebook',
            'provider_id.required' => 'provider_id_is_required',
            'provider_id.string' => 'provider_id_must_be_a_string',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'full_name' => [
                'example' => 'John Doe',
            ],
            'email' => [
                'example' => 'user@example.com',
            ],
            'password' => [
                'example' => '12345678Aa#',
            ],
            'fcm_token' => [
                'example' => 'abc123xyz',
            ],
            'provider' => [
                'example' => 'google',
            ],
            'provider_id' => [
                'example' => 'google-id-123456',
            ],
        ];
    }
}
