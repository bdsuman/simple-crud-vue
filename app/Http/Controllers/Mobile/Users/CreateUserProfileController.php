<?php

namespace App\Http\Controllers\Mobile\Users;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProfileQuestionSubmission;
use App\Http\Requests\Mobile\User\VoiceRequest;
use App\Http\Controllers\Mobile\Users\UserController;
use App\Http\Requests\Mobile\User\StoreUserProfileRequest;

/**
 * User Setup — Basic Info, Topics, or Voice
 *
 * Handles a single step of the user setup flow, selected via the `key` parameter.
 * Supported steps are:
 * - `basic_info`: Update gender and date of birth.
 * - `topic_info`: Create one or more topic scale submissions (bulk insert).
 * - `voice`: Upload voice files and metadata (validated by `VoiceRequest`).
 *
 * For `voice`, send the request as **multipart/form-data**.
 *
 * @group User
 * @authenticated
 *
 * @header Accept application/json
 * @header language optional Locale for response messages (e.g., "en"). Defaults to app fallback.
 *
 * @bodyParam key string required Which setup step to execute. Allowed values: basic_info, topic_info, voice. Example: basic_info
 *
 * @bodyParam gender string required when:key=basic_info The user's gender. Example: male
 * @bodyParam date_of_birth date required when:key=basic_info Date of birth in Y-m-d format. Example: 1995-08-12
 *
 * @bodyParam items array required when:key=topic_info Array of topic objects to upsert.
 * @bodyParam items[].session_topic_id integer required when:key=topic_info Distinct IDs that must exist in user_profile_questions.id. Example: 2
 * @bodyParam items[].scale integer nullable when:key=topic_info Numeric score per topic (1–10). If null or omitted, defaults to 0. Example: 4
 * @bodyParam items[].is_selected boolean nullable when:key=topic_info Whether the topic is selected. Defaults to false if omitted. Example: true
 *
 * @bodyParam voice file required when:key=voice The voice file to upload. Allowed types: m4a, mp4, 3gp. Max 30 MB.
 * @bodyParam cloned_voice file required when:key=voice The cloned voice file to upload. Allowed type: mp3. Max 30 MB.
 * @bodyParam voice_title string required when:key=voice A short title for the uploaded voice (max 150 chars). Example: My Voice Recording
 * @bodyParam voice_text string required when:key=voice Text associated with the voice recording. Example: Hello, this is a sample voice text.
 * @bodyParam voice_status string required when:key=voice Status of the voice. Allowed values: approved, failed. Example: approved
 *
 * @response 200 scenario="basic_info success" {
 *   "status": true,
 *   "message": "Profile update",
 *   "data": []
 * }
 *
 * @response 200 scenario="topic_info success" {
 *   "status": true,
 *   "message": "profile_submission_created",
 *   "data": []
 * }
 *
 * @response 200 scenario="voice success" {
 *   "status": true,
 *   "message": "user_voice_uploaded_successfully",
 *   "data": {
 *     "id": 42,
 *     "name": "Jane Doe",
 *     "voice_title": "My Voice Recording",
 *     "voice_text": "Hello, this is a sample voice text.",
 *     "voice_status": "approved",
 *     "voice": "users/voice/abcd1234.m4a",
 *     "cloned_voice": "users/tts/efgh5678.mp3",
 *     "...": "..."
 *   }
 * }
 *
 * @response 422 scenario="validation error (missing key)" {
 *   "message": "The given data was invalid.",
 *   "errors": {
 *     "key": [
 *       "The key field is required."
 *     ]
 *   }
 * }
 *
 * @response 422 scenario="validation error (basic_info)" {
 *   "message": "The given data was invalid.",
 *   "errors": {
 *     "gender": [
 *       "The gender field is required."
 *     ],
 *     "date_of_birth": [
 *       "The date of birth field is required."
 *     ]
 *   }
 * }
 *
 * @response 422 scenario="validation error (topic_info)" {
 *   "message": "The given data was invalid.",
 *   "errors": {
 *     "items": [
 *       "The items field is required."
 *     ],
 *   "items.0.session_topic_id": [
 *        "The selected items.0.session_topic_id is invalid."
 *        ],
 *   }
 * }
 *
 * @response 422 scenario="validation error (voice)" {
 *   "message": "The given data was invalid.",
 *   "errors": {
 *     "voice": [
 *       "please_upload_a_voice_file"
 *     ],
 *     "cloned_voice": [
 *       "please_upload_a_cloned_voice_file"
 *     ],
 *     "voice_title": [
 *       "a_title_for_the_voice_is_required"
 *     ],
 *     "voice_text": [
 *       "voice_text_is_required"
 *     ],
 *     "voice_status": [
 *       "the_voice_status_is_required"
 *     ]
 *   }
 * }
 *
 * @response 401 { "message": "Unauthenticated." }
 */
class CreateUserProfileController extends Controller
{
    /**
     * User Setup
     *
     * @param StoreUserProfileRequest $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $request->validate(['key' => 'required|in:basic_info,topic_info,voice']);

        return match ($request->key) {
            'basic_info'    => $this->updateUserProfile($request),
            'topic_info'    => $this->updateTopicInfo($request),
            'voice'         => $this->updateVoiceInfo(),
        };
    }

    private function updateUserProfile($request)
    {
        $request->validate([
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);

        $language = app('language');
        $user = Auth::user();
        $user->update($request->only('gender', 'date_of_birth'));

        return success_response(
            [],
            false,
            __('Profile update', [], $language)
        );
    }

    private function updateTopicInfo($request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.session_topic_id' => [
                'required',
                'integer',
                Rule::exists('user_profile_questions', 'id'),
            ],
            'items.*.scale'       => ['nullable', 'integer', 'between:0,10'],
            'items.*.is_selected' => ['nullable', 'boolean'],
        ]);

        $items   = $validated['items'];
        $userId  = auth()->id();
        $now     = now();

        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                'user_id'                   => $userId,
                'user_profile_question_id'  => $item['session_topic_id'],
                'scale'                     => $item['scale'] ?? 1,
                'is_selected'               => $item['is_selected'] ?? false,
                'created_at'                => $now,
                'updated_at'                => $now,
            ];
        }

        DB::transaction(function () use ($rows, $userId) {
            UserProfileQuestionSubmission::where('user_id', $userId)->delete();
            UserProfileQuestionSubmission::insert($rows);
        });

        return success_response(
            [],
            false,
            __('profile_submission_created')
        );
    }

    private function updateVoiceInfo()
    {
        $voiceRequest = app(VoiceRequest::class);

        return app(UserController::class)->uploadVoice($voiceRequest);
    }
}
