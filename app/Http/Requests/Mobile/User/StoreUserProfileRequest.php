<?php

namespace App\Http\Requests\Mobile\User;

use App\Enums\UserGenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Adjust if you need authorization checks
        return true;
    }

    public function rules(): array
    {
        return [
            'key'  => 'required|in:basic_info,topic_info,voice',
        ];
    }
}
