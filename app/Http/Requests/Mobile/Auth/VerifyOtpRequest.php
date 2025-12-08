<?php

namespace App\Http\Requests\Mobile\Auth;

use App\Enums\OtpTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class VerifyOtpRequest extends FormRequest
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
            'otp'   => ['required', 'digits:6'],
            'otp_type' => ['required', new Enum(OtpTypeEnum::class)],
            'fcm_token' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'email_is_required',
            'email.email' => 'invalid_email_format',
            'email.exists' => 'email_not_found',
            'otp.required' => 'otp_is_required',
            'otp.digits' => 'otp_must_be_6_digits',
            'otp_type.required' => 'otp_type_is_required',
            'otp_type.enum' => 'invalid_otp_type',
            'fcm_token.required' => 'fcm_token_is_required',
            'fcm_token.string' => 'invalid_fcm_token_format',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'example' => 'user@example.com',
            ],
            'otp' => [
                'description' => '6-digit One-Time Password sent to your email.',
                'example' => '123456',
            ],
            'otp_type' => [
                'example' => OtpTypeEnum::REGISTRATION->value,
            ],
            'fcm_token' => [
                'example' => 'abc123xyz',
            ],
        ];
    }
}
