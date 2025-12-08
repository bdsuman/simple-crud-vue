<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Enums\OtpTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Auth\ResetPasswordRequest;
use App\Http\Requests\Mobile\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

/**
 * @group Auth
 * @subgroup Forgot Password
 * @unauthenticated
 */
class ForgotPasswordController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    /**
     * Send OTP for password reset
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::getActiveUserByEmail($validated['email']);

        if (!$user) {
            return error_response('email_is_not_active', 401);
        }

        $sendOTP = $this->otpService->requestOtp([
            'otp_type' => OtpTypeEnum::FORGOT_PASSWORD->value,
            'email'    => $user->email,
        ]);

        return success_response([], false, $sendOTP->getData()->message);
    }

    /**
     * Reset password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $grantKey = "reset:grant:{$validated['reset_token']}";
        $rawGrant = Redis::get($grantKey);

        if (!$rawGrant) {
            return error_response('invalid_or_expired_token', 422);
        }

        $grant = json_decode($rawGrant, true) ?: [];
        if (($grant['purpose'] ?? null) !== 'password_reset') {
            return error_response('invalid_or_expired_token', 422);
        }

        $user = User::find($grant['uid'] ?? 0);
        if (!$user || !hash_equals((string)$user->email, (string)($grant['email'] ?? ''))) {
            return error_response('unauthorized', 401);
        }

        DB::beginTransaction();
        try {
            $user->password = Hash::make($validated['password']);
            $user->save();

            Redis::del($grantKey);

            DB::commit();
            return success_response([], false, 'password_reset');
        } catch (\Throwable $e) {
            DB::rollBack();
            return error_response('something_went_wrong', 500, ['exception' => $e->getMessage()]);
        }
    }
}
