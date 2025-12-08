<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\UserAccountStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Mobile\UserResource;
use App\Http\Requests\Mobile\Auth\LoginRequest;
use App\Http\Resources\Mobile\Auth\AuthUserResource;

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
        $email       = $credentials['email'] ?? null;
        $password    = $credentials['password'] ?? null;

        $user = User::getUserByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return error_response('unauthorized', 401);
        }

        if ($user->status === UserAccountStatusEnum::PENDING) {
            return error_response('otp_not_verified', 403);
        }

        if ($user->role !== UserRoleEnum::USER) {
            return error_response('forbidden', 403);
        }

        if ($user->status !== UserAccountStatusEnum::ACTIVE) {
            return error_response('unauthorized', 403);
        }

        // Issue a fresh Sanctum token for mobile clients
        $user->tokens()->delete();
        $token = $user->createToken('mobile')->plainTextToken;

        if (!empty($credentials['fcm_token'])) {
            $user->devices()->firstOrCreate([
                'fcm_token' => $credentials['fcm_token'],
            ]);
        }

        $lang = $request->header('X-Language', $request->header('language'));
        if (!empty($lang)) {
            $user->update(['language' => $lang]);
        }

        // save activity log
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->event('login:mobile')
            ->createdAt(now()->subDays(10))
            ->tap(function ($activity) {
                $activity->log_name = 'user_activity';
            })
            ->log('user_logged_in');

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
     * @group Auth
     * @authenticated
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $credentials = $request->only('fcm_token');
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        if ($user && !empty($credentials['fcm_token'])) {
            $user->devices()->where('fcm_token', $credentials['fcm_token'])->delete();
        }

        return success_response([], false, 'logged_out');
    }

    /**
     * Refresh a token
     *
     * @group Auth
     * @authenticated
     *
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return error_response('unauthenticated', 401);
        }

        optional($user->currentAccessToken())->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->respondWithToken($token, $user);
    }

    protected function respondWithToken(string $token, User $user): JsonResponse
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
}
