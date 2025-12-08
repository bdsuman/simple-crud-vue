<?php

namespace App\Http\Controllers\Mobile\Users;

use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Enums\UserGenderEnum;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\UpdateUserProfileService;
use App\Http\Resources\Mobile\UserResource;
use App\Http\Requests\Mobile\User\VoiceRequest;
use App\Http\Requests\Mobile\User\UpdateProfileRequest;
use App\Http\Requests\Mobile\User\ChangePasswordRequest;

/**
 * @group User
 * @authenticated
 */
class UserController extends Controller
{
    use UploadAble;
    /**
     * Get user setup options
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getOptionsData(Request $request): JsonResponse
    {
        $lang = $request->header('language', 'de');
        $data = [];

        $data['gender_options'] = array_map(fn($i) => [
            'label' => trans($i->value, [], $lang),
            'value' => $i->value
        ], UserGenderEnum::cases());

        return success_response($data, false, 'user_setup_options_fetched_successfully');
    }

    /**
     * Profile
     *
     * @group User
     * @authenticated
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        return success_response(new UserResource(Auth::user()), false, 'user_profile_fetched_successfully');
    }

    /**
     * Voice Upload
     *
     * The uploaded file will be stored.
     *
     * @response 200 scenario="Voice uploaded successfully" {
     *   "success": true,
     *   "message": "user_voice_uploaded_successfully",
     *   "data": {}
     * }
     */

    public function uploadVoice(VoiceRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $disk = config('filesystems.default');

        // Delete old files if exist
        foreach (['voice', 'cloned_voice'] as $field) {
            if ($user->{$field}) {
                $this->deleteFile($user->{$field}, $disk);
            }
        }

        // Prepare updated data
        $updateData = [
            'voice_title'  => $request->voice_title,
            'voice_text'   => $request->voice_text,
            'voice_status' => $request->voice_status,
        ];

        // Upload new files if provided
        if ($request->hasFile('voice')) {
            $updateData['voice'] = $this->uploadFile(
                $request->file('voice'),
                'users/voice',
                $disk
            );
        }

        if ($request->hasFile('cloned_voice')) {
            $updateData['cloned_voice'] = $this->uploadFile(
                $request->file('cloned_voice'),
                'users/tts',
                $disk
            );
        }

        $user->update($updateData);

        return success_response(
            new UserResource($user->refresh()),
            false,
            'user_voice_uploaded_successfully'
        );
    }

    /**
     * Update User Profile
     *
     * Update the authenticated user's profile including full name, gender, date of birth, and avatar.
     *
     * @bodyParam full_name string required Full name of the user. Example: John Doe
     * @bodyParam gender string required Gender of the user. One of: male, female, other. Example: male
     * @bodyParam date_of_birth date required Date of birth in YYYY-MM-DD format. Example: 1990-05-15
     * @bodyParam avatar file optional Profile avatar image. Allowed types: png, jpg. Max size: 10MB.
     *
     * @response 200 {
     *   "status": true,
     *   "message": "User profile updated successfully",
     *   "data": {
     *     "id": 1,
     *     "full_name": "John Doe",
     *     "gender": "male",
     *     "date_of_birth": "1990-05-15",
     *     "avatar": "https://…/avatar.jpg",
     *     "email": "john@example.com",
     *     "created_at": "2025-10-09T12:00:00Z",
     *     "updated_at": "2025-10-09T12:15:00Z"
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "full_name": ["full_name_required"],
     *     "gender": ["gender_required"],
     *     "date_of_birth": ["date_of_birth_required"]
     *   }
     * }
     * @response 401 {"message":"Unauthenticated."}
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = (new UpdateUserProfileService())->updateProfile($request);

        return success_response(
            new UserResource($user),
            false,
            __("user_profile_updated_successfully")
        );
    }

    /**
     * Change User Password
     *
     * Change the authenticated user's password. Requires current password validation and ensures the new password is different.
     *
     * @bodyParam current_password string required Current password. Example: oldpassword123
     * @bodyParam password string required New password. Must be different from current password. Example: NewPass123!
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Password updated successfully",
     *   "data": []
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "The current password is incorrect",
     *   "errors": {
     *     "current_password": ["The current password is incorrect"]
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "The new password cannot be the same as the current password",
     *   "errors": {
     *     "password": ["The new password cannot be the same as the current password"]
     *   }
     * }
     * @response 401 {"message":"Unauthenticated."}
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return error_response(
                __("The current password is incorrect"),
                422
            );
        }

        if (Hash::check($request->password, $user->password)) {
            return error_response(
                __("The new password cannot be the same as the current password"),
                422
            );
        }

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return success_response(
            [],
            false,
            __("password_updated_successfully")
        );
    }

    /**
     * Voice Delete
     *
     * This endpoint deletes a user voice by its ID from the ElevenLabs service.
     *
     * @response 200 scenario="Voice deleted successfully" {
     *   "success": true,
     *   "message": "user_voice_deleted_successfully",
     * }
     */
    public function deleteVoice(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $this->deleteFile($user->voice, config('filesystems.default'));
        $this->deleteFile($user->cloned_voice, config('filesystems.default'));

        // if ($user->eleven_lab_voice_id) {
        //     DeleteVoiceJob::dispatch($user->id);
        // }

        $user->update([
            'voice'                => null,
            'voice_title'          => null,
            'voice_status'         => null,
            'eleven_lab_voice_id'  => null,
            'cloned_voice'         => null,
            'voice_text'           => null,
        ]);

        return success_response(
            new UserResource($user->fresh()),
            false,
            'user_voice_deleted_successfully'
        );
    }

    /**
     * Account Delete
     *
     * This endpoint deletes a user account.
     *
     * @response 200 scenario="Account deleted successfully" {
     *   "success": true,
     *   "message": "user_account_deleted_successfully",
     * }
     */
    public function deleteAccount()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $flag= 'DELETED_' .time().'_';
        //email flag deleted with timestamp added
        $user->email            = $flag.$user->email;
        $user->facebook_user_id = $flag.$user->facebook_user_id;
        $user->apple_user_id    = $flag.$user->apple_user_id;
        $user->google_user_id   = $flag.$user->google_user_id;
        $user->save();

        $user->delete();

        return success_response(
            [],
            false,
            'user_account_deleted_successfully'
        );
    }

    public function triggerNotification()
    {
        // Get the currently authenticated user
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Ensure the user is authenticated
        if ($user) {
            // Send the FCM notification
            $user->fcmNotify(
                'DeepGrow Reminder 🌱',
                'Your daily mindfulness session is ready.',
                ['screen' => 'session_created', 'session_id' => 24,'path_id' => 1,'path_name' => 'Path name']
            );
            return response()->json(['message' => 'Notification sent successfully.']);
        }

        // If the user is not authenticated, return an error
        return response()->json(['message' => 'User not authenticated.'], 401);
    }
}
