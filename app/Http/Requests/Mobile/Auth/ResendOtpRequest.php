<?php

namespace App\Http\Requests\Mobile\Auth;

use App\Enums\OtpTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class ResendOtpRequest extends FormRequest
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
            'otp_type' => ['required', new Enum(OtpTypeEnum::class)]
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'email_is_required',
            'email.email' => 'invalid_email_format',
            'email.exists' => 'email_not_found',
            'otp_type.required' => 'otp_type_is_required',
            'otp_type.enum' => 'invalid_otp_type',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'example' => 'user@example.com',
            ],
            'otp_type' => [
                'example' => OtpTypeEnum::REGISTRATION->value,
            ],
        ];
    }
}
