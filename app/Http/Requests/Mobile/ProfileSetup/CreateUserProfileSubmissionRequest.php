<?php

namespace App\Http\Requests\Mobile\ProfileSetup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserProfileSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_ids'      => ['required', 'array', 'min:1'],
            'question_ids.*'    => ['required', 'integer', 'distinct', Rule::exists('user_profile_questions', 'id')],
            'scale'           => ['required', 'array', 'min:1'],
            'scale.*'         => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'question_ids.required' => __('question_id_required'),
            'question_ids.*.exists' => __('question_id_invalid'),
            'scale.required'      => __('scale_required'),
            'scale.*.required'    => __('scale_required'),
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'question_ids' => [
                'description' => 'Array of user profile question IDs to answer. Each must exist.',
                'example'     => [12, 45, 78],
                'required'    => true,
            ],
            'scale' => [
                'description' => 'Numeric score between 1 and 10 applied to these answers.',
                'example'     => 7,
                'required'    => true,
            ],
        ];
    }
}
