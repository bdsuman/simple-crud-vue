<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\{LoginRequest, ChangePasswordRequest, UpdateProfileRequest};
use App\Http\Resources\Api\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * @group Auth
 */
class AuthController extends Controller
{
    /**
     * Login
     * @unauthenticated 
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::getUserByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return error_response('unauthorized', 401);
        }



        // Issue a fresh Sanctum token
        $user->tokens()->delete();
        $token = $user->createToken('admin')->plainTextToken;

        return $this->respondWithToken($token, $user);
    }

    /**
     * User Profile
     * 
     * @group Auth
     * @authenticated
     * 
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return success_response(new UserResource($request->user()));
    }

    /**
     * Logout
     * 
     * @group Auth
     * @authenticated
     * 
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return success_response([], false, 'logged_out');
    }

    /**
     * Refresh a token.
     * 
     * @group Auth
     * @authenticated
     * 
     * @return JsonResponse
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return error_response('unauthenticated', 401);
        }

        optional($user->currentAccessToken())->delete();

        $token = $user->createToken('admin')->plainTextToken;

        return $this->respondWithToken($token, $user);
    }

    protected function respondWithToken(string $token, User $user)
    {
        $ttl = config('sanctum.expiration');

        return success_response([
            'message' => 'login_successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttl ? $ttl * 60 : null,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update Profile
     * 
     * @group Auth
     * @authenticated
     * 
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update full_name and language
        $user->full_name = $validated['full_name'];
        $user->language = $validated['language'] ?? $user->language;

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return success_response(new UserResource($user), false, 'profile_updated');
    }

    /**
     * Change Password
     * 
     * @group Auth
     * @authenticated
     * 
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return error_response('current_password_is_incorrect', 422);
        }

        // Update with new password
        $user->password = Hash::make($validated['password']);
        $user->save();

        return success_response(new UserResource($user), false, 'password_changed');
    }
}
