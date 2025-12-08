<?php

namespace App\Http\Requests\Api;

use App\Enums\AppLanguageEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|min:2|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'language' => 'nullable|string|in:' . implode(',', array_column(AppLanguageEnum::cases(), 'value')),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'field_input_is_missing',
            'full_name.min' => 'must_be_at_least_2_characters',
            'password.min' => 'password_must_be_at_least_8_characters',
            'password.confirmed' => 'passwords_do_not_match',
            'avatar.image' => 'avatar_must_be_an_image',
            'avatar.mimes' => 'avatar_must_be_jpg_or_png',
            'avatar.max' => 'avatar_size_exceeds_limit',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'validation_failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
