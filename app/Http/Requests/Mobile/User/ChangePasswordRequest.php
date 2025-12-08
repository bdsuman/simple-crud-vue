<?php

namespace App\Http\Requests\Mobile\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow only logged-in users to change their own password
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                Password::defaults(),
            ],
        ];
    }

    /**
     * Custom messages (optional)
     */
    public function messages(): array
    {
        return [
            'current_password.required' => __('The current password field is required.'),
            'current_password.current_password' => __('The current password is incorrect.'),
            'password.required' => __('The new password field is required.'),
            'password.confirmed' => __('The new password confirmation does not match.'),
        ];
    }
}
