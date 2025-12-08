<?php

namespace App\Http\Requests\Mobile\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
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
                //     ->rfcCompliant(strict: false)
                //     ->validateMxRecord()
                //     ->preventSpoofing()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'email_is_required',
            'email.email' => 'invalid_email_format',
            'email.exists' => 'email_not_found',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'example' => 'user@example.com',
            ],
        ];
    }
}
