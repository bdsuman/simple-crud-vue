<?php

namespace App\Http\Requests\Api\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:250'],
            'job_title'   => ['required', 'string', 'max:250'],
            'title'       => ['required', 'string', 'max:250'],
            'content'     => ['required', 'string', 'max:500'],
            'avatar'      => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:20480'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'author_name.required' => 'author_name_is_required',
            'author_name.max'      => 'author_name_must_not_exceed_250_characters',

            'job_title.required'   => 'job_title_is_required',
            'job_title.max'        => 'job_title_must_not_exceed_250_characters',

            'title.required'       => 'title_is_required',
            'title.max'            => 'title_must_not_exceed_250_characters',

            'content.required'     => 'content_is_required',
            'content.max'          => 'content_must_not_exceed_500_characters',

            'avatar.file'          => 'avatar_must_be_a_file',
            'avatar.mimes'         => 'avatar_must_be_jpg_jpeg_or_png',
            'avatar.max'           => 'avatar_must_not_exceed_20MB',
        ];
    }

    /**
     * Example body params for API docs
     */
    public function bodyParameters(): array
    {
        return [
            'author_name' => [
                'example' => 'Jane Smith',
                'description' => 'Updated full name of the testimonial author'
            ],
            'job_title' => [
                'example' => 'Product Manager',
                'description' => 'Updated job title or designation of the author'
            ],
            'title' => [
                'example' => 'Outstanding Support!',
                'description' => 'Updated title or heading of the testimonial'
            ],
            'content' => [
                'example' => 'The team provided exceptional support throughout the project.',
                'description' => 'Updated testimonial content (max 500 characters)'
            ],
            'avatar' => [
                'description' => 'Optional new avatar file (jpg, jpeg, png, max 20MB)'
            ],
        ];
    }
}
