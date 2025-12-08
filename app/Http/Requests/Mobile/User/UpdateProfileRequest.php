<?php

namespace App\Http\Requests\Mobile\User;

use App\Enums\UserGenderEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name'         => ['required', 'string'],
            'gender'            => ['required', Rule::in(array_column(UserGenderEnum::cases(), 'value'))],
            'date_of_birth'     => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'avatar'            => ['sometimes', 'nullable', 'file', 'mimes:png,jpg', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'      => 'full_name_required',
            'gender.required'         => 'gender_required',
            'gender.in'               => 'gender_invalid',
            'date_of_birth.required'  => 'date_of_birth_required',
            'date_of_birth.before_or_equal' => 'date_of_birth_cannot_be_in_future',
            'date_of_birth.after'     => 'date_of_birth_too_old',
            'date_of_birth.date_format'     => 'date_of_birth_format_invalid_is_valid_Y-m-d',
            'avatar.file'             => 'avatar_must_be_file',
            'avatar.mimes'            => 'avatar_invalid_mime',
            'avatar.max'               => 'avatar_must_not_exceed_50MB',
        ];
    }
}
