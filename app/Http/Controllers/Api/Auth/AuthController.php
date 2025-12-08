<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\Api\AuthUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Auth
 * @unauthenticated 
 */
class AuthController extends Controller
{
    /**
     * Login
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
        return success_response(new AuthUserResource($request->user()));
    }

    /**
     * Logout
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
            'user' => new AuthUserResource($user),
        ]);
    }

    /**
     * Update Profile
     * 
     * @group Auth
     * @authenticated
     * 
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $requestData = request()->only(['full_name', 'language']);

        $user->fill($requestData);
        $user->save();

        return success_response(new AuthUserResource($user), false, 'profile_updated');
    }
}
