<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Models\User;
use App\Enums\OtpTypeEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Str;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Enums\UserAccountStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Mobile\Auth\RegisterRequest;
use App\Http\Resources\Mobile\UserResource;

/**
 * @group Auth
 * @unauthenticated
 */
class RegisterController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    /**
     * Registration
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $data = [
                'full_name'     => $validated['full_name'],
                'role'          => UserRoleEnum::USER,
                'status'        => UserAccountStatusEnum::PENDING,
                'agreed_to_tos' => true,
                'provider'      => $request->provider ?? null,
                'provider_id'   => $request->provider_id ?? null,
            ];

            if (isset($validated['provider']) && isset($validated['provider_id'])) {
                $data['login_type'] = match ($validated['provider']) {
                    'google' => 'google',
                    'apple' => 'apple',
                    'facebook' => 'facebook',
                    default => 'regular',
                };

                if (in_array($validated['provider'], ['google', 'apple', 'facebook'])) {
                    $data[$validated['provider'] . '_email'] = Str::lower(trim($validated['email']));
                    $data[$validated['provider'] . '_user_id'] = $validated['provider_id'];

                    unset($data['password']);

                    $data['provider'] = $validated['provider'];
                    $data['provider_id'] = $validated['provider_id'];
                }
            } else {
                $data['password'] = Hash::make($validated['password']);
            }

            $email = Str::lower(trim($validated['email']));
            $user = User::firstOrCreate(
                ['email' => $email],
                $data
            );

            if (!$user->relationLoaded('profile')) {
                $user->profile()->create();
            }

            $language = $request->header('X-Language', $request->header('language'));
            if ($language) {
                $user->update(['language' => $language]);
            }

            if ($validated['provider'] ?? null && $validated['provider_id'] ?? null) {
                $user->tokens()->delete();
                $token = $user->createToken('mobile')->plainTextToken;

                $user->update(['status' => UserAccountStatusEnum::ACTIVE, 'email_verified_at' => now()]);

                DB::commit();

                return $this->respondWithToken($token, $user);
            } else {
                $this->otpService->requestOtp([
                    'otp_type' => OtpTypeEnum::REGISTRATION->value,
                    'email'    => $user->email,
                ]);
            }

            DB::commit();

            return success_response(
                __('otp_sent'),
                false
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);

            return server_error(e: [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
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
