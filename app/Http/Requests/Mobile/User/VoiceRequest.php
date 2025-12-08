<?php

namespace App\Http\Requests\Mobile\User;

use Illuminate\Foundation\Http\FormRequest;

class VoiceRequest extends FormRequest
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
            'voice' => ['required', 'file', 'mimes:m4a,mp4,3gp', 'max:30240'],
            'cloned_voice' => ['required', 'file', 'mimes:mp3', 'max:30240'],
            'voice_title' => ['required', 'string', 'max:150'],
            'voice_text' => ['required', 'string'],
            'voice_status' => ['required', 'in:approved,failed'],
        ];
    }

    /**
     * Describe the body parameters for API docs (Scribe).
     */
    public function bodyParameters(): array
    {
        return [
            'voice' => [
                'description' => 'The voice file to upload (M4A only, max 30 MB).',
            ],
            'cloned_voice' => [
                'description' => 'The cloned voice file to upload (MP3 only, max 30 MB).',
            ],
            'voice_title' => [
                'description' => 'A short title for the uploaded voice (max 150 characters).',
                'example'     => 'My Voice Recording',
            ],
            'voice_text' => [
                'description' => 'Text content associated with the voice recording.',
                'example'     => 'Hello, this is a sample voice text.',
            ],
            'voice_status' => [
                'description' => 'Status of the voice. Allowed values: approved, failed.',
                'example'     => 'approved',
            ],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'voice.required' => 'please_upload_a_voice_file',
            'voice.file'     => 'the_voice_must_be_a_valid_file',
            'voice.mimes'    => 'only_M4A_files_are_allowed',
            'voice.max'      => 'the_voice_file_may_not_be_larger_than_30MB',

            'cloned_voice.required' => 'please_upload_a_cloned_voice_file',
            'cloned_voice.file'     => 'the_cloned_voice_must_be_a_valid_file',
            'cloned_voice.mimes'    => 'only_MP3_files_are_allowed_for_cloned_voice',
            'cloned_voice.max'      => 'the_cloned_voice_file_may_not_be_larger_than_30MB',

            'voice_title.required'  => 'a_title_for_the_voice_is_required',
            'voice_title.string'    => 'the_voice_title_must_be_a_text_value',
            'voice_title.max'       => 'the_voice_title_may_not_exceed_150_characters',

            'voice_text.required'    => 'voice_text_is_required',
            'voice_text.string'      => 'the_voice_text_must_be_a_text_value',

            'voice_status.required'  => 'the_voice_status_is_required',
            'voice_status.in'        => 'the_voice_status_must_be_either_approved_or_failed',
        ];
    }
}
