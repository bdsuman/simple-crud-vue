<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\OtpTypeEnum;
use App\Mail\Mobile\WelcomeMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Enums\UserAccountStatusEnum;
use App\Mail\Mobile\ChangeEmailMail;
use Illuminate\Support\Facades\Mail;
use App\Enums\OtpDestinationTypeEnum;
use Illuminate\Support\Facades\Redis;
use App\Mail\Mobile\PasswordResetMail;
use App\Mail\Mobile\SendRegistrationMail;
use App\Models\PersonalityProfileUserScore;
use App\Http\Resources\Mobile\Auth\AuthUserResource;

class OtpService
{
    const EXPIRATION_TIME = 2; // minutes
    const GRANT_TIME = 10; // minutes

    // ---- Helpers ------------------------------------------------------------

    private function otpKey(int $userId, string $otpDestination): string
    {
        return "otp:{$userId}:{$otpDestination}";
    }

    private function readOtpPayload(int $userId, string $otpDestination): ?array
    {
        $key = $this->otpKey($userId, $otpDestination);
        $raw = Redis::get($key);
        return $raw ? json_decode($raw, true) : null;
    }

    private function writeOtpPayload(int $userId, string $otpDestination, array $payload): void
    {
        $key = $this->otpKey($userId, $otpDestination);
        $ttlSeconds = self::EXPIRATION_TIME * 60;
        // Store atomically with TTL
        Redis::setex($key, $ttlSeconds, json_encode($payload));
    }

    private function ttlSeconds(int $userId, string $otpDestination): int
    {
        $key = $this->otpKey($userId, $otpDestination);
        $ttl = Redis::ttl($key);
        return $ttl > 0 ? $ttl : 0;
    }

    // ---- Public API (kept compatible with your controller/routes) -----------

    // Request OTP
    public function requestOtp(array $request)
    {
        $user = $this->getUserByEmail($request);
        $otpDestination = $this->getOtpDestinationType($request);
        $newEmail = $request['new_email'] ?? null;

        if (!$user) {
            return error_response('invalid_user', 404);
        }

        if ($otp = $this->otpAlreadySent($user->id, $otpDestination)) {
            $remainingSeconds = $otp['remaining_seconds'];
            $otpData = [
                'otp_type'             => $otp['otp_type'],
                'otp_destination_type' => $otp['otp_destination_type'],
                'email'                => $otp['email'],
                'expire_at'            => $otp['expire_at'],       // Y-m-d H:i:s
                'remaining_seconds'    => $remainingSeconds,
            ];
            return success_response($otpData, false, 'otp_already_sent');
        }

        $newOtp = random_int(100000, 999999);

        $this->createOtp($request, $user->id, $newOtp, $otpDestination);
        $this->sendOtp($request['otp_type'], $otpDestination, $user, $newOtp, $newEmail);

        $otpData = [
            'otp_type'             => $request['otp_type'],
            'otp_destination_type' => $otpDestination,
            'email'                => $newEmail,
            'expire_at'            => now()->addMinutes(self::EXPIRATION_TIME)->format('Y-m-d H:i:s'),
            'remaining_seconds'    => self::EXPIRATION_TIME * 60,
        ];

        return success_response($otpData, false, 'otp_sent');
    }

    // Verify OTP
    public function verifyOtp(array $request): JsonResponse
    {
        $user = $this->getUserByEmail($request);

        if (!$user) {
            return error_response('invalid_user', 404);
        }

        if (!$this->isOtpValid($request, $user->id)) {
            return error_response('invalid_otp', 400);
        }

        return $this->processOtpVerification($request, $user);
    }

    // ---- Internals (ported from DB to Redis) --------------------------------

    private function getUserByEmail(array $request): ?User
    {
        return User::where('email', $request['email'] ?? '')->first();
    }

    private function getOtpDestinationType(array $request): string
    {
        return OtpDestinationTypeEnum::EMAIL->value;
    }

    private function otpAlreadySent(int $userId, string $otpDestination): ?array
    {
        $payload = $this->readOtpPayload($userId, $otpDestination);
        if (!$payload) {
            return null;
        }

        $remaining = $this->ttlSeconds($userId, $otpDestination);
        if ($remaining <= 0) {
            return null;
        }

        // Return a shape similar to your original response expectations
        return [
            'otp_type'             => $payload['otp_type'] ?? null,
            'otp_destination_type' => $otpDestination,
            'email'                => $payload['email'] ?? null,
            'expire_at'            => Carbon::now()->addSeconds($remaining)->format('Y-m-d H:i:s'),
            'remaining_seconds'    => $remaining,
        ];
    }

    private function createOtp(array $request, int $userId, string $otp, string $otpDestination): void
    {
        $expireAt = now()->addMinutes(self::EXPIRATION_TIME)->format('Y-m-d H:i:s');

        $payload = [
            'user_id'              => $userId,
            'otp'                  => (string) $otp,
            'otp_type'             => $request['otp_type'],
            'otp_destination_type' => $otpDestination,
            'expire_at'            => $expireAt,
            'email'                => $request['email'] ?? null,
            'new_email'            => $request['new_email'] ?? null,
        ];

        $this->writeOtpPayload($userId, $otpDestination, $payload);
    }

    private function sendOtp(string $otpType, string $otpDestination, User $user, int $otp, $newEmail): void
    {
        $this->sendOtpEmail($otpType, $user->email, $user, $otp, $newEmail);
    }

    private function sendOtpEmail(string $otpType, string $email, User $user, int $otp, $newEmail): void
    {

        switch ($otpType) {
            case OtpTypeEnum::REGISTRATION->value:
                Mail::to($email)->send(new SendRegistrationMail($user, $otp, self::EXPIRATION_TIME));
                break;
            case OtpTypeEnum::FORGOT_PASSWORD->value:
                Mail::to($email)->send(new PasswordResetMail($user, $otp, self::EXPIRATION_TIME));
                break;
                // case OtpTypeEnum::CHANGE_EMAIL->value:
                //     Mail::to($email)->send(new ChangeEmailMail($user, $newEmail, $otp, self::EXPIRATION_TIME));
                //     break;
        }
    }

    private function isOtpValid(array $request, int $userId): bool
    {
        $otpDestination = $this->getOtpDestinationType($request);
        $payload = $this->readOtpPayload($userId, $otpDestination);

        if (!$payload) {
            return false;
        }

        // TTL must still be positive
        if ($this->ttlSeconds($userId, $otpDestination) <= 0) {
            return false;
        }

        // Ensure the channel identity matches
        $channelProvided = $request['email'];
        $channelStored   = $payload['email'];

        return
            (string)($request['otp'] ?? '') === (string)($payload['otp'] ?? '') &&
            ($request['otp_type'] ?? null) === ($payload['otp_type'] ?? null) &&
            $channelProvided !== null &&
            $channelProvided === $channelStored;
    }

    private function processOtpVerification(array $request, User $user): JsonResponse
    {
        $otpType = $request['otp_type'] ?? null;

        // For security, delete OTP once it's been used.
        $this->deleteOtp($request, $user->id, true);

        $payload = [];

        if ($otpType === OtpTypeEnum::REGISTRATION->value) {
            $user->update([
                'email_verified_at' => now(),
                'status'            => UserAccountStatusEnum::ACTIVE,
            ]);

            // Sending welcome email to the user
            Mail::to($user->email)->send(new WelcomeMail($user));

            // Auto-login after registration OTP verification
            return $this->loginUserAfterOtpVerification($user, $request['fcm_token']);
        }

        if ($otpType === OtpTypeEnum::CHANGE_EMAIL->value && !empty($request['new_email'])) {
            $user->update([
                'email'             => $request['new_email'],
                'email_verified_at' => now(),
            ]);
        }

        if ($otpType === OtpTypeEnum::FORGOT_PASSWORD->value) {
            // Mint a single-use reset grant
            $resetToken = $this->mintResetGrant($user, $request);
            $payload['reset_token'] = $resetToken;
        }

        // client will use `reset_token` in the next call
        return success_response($payload, false, 'otp_verified');
    }

    // Login User After OTP Verification
    protected function loginUserAfterOtpVerification(User $user, string $fcmToken): JsonResponse
    {
        $user->tokens()->delete();
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
            'user' => new AuthUserResource($user),
        ]);
    }

    private function mintResetGrant(User $user, array $request): string
    {
        $token = bin2hex(random_bytes(32));

        $grant = [
            'uid'       => $user->id,
            'email'     => $user->email,
            'otp_type'  => $request['otp_type'] ?? null,
            'iat'       => now()->timestamp,
            'purpose'   => 'password_reset',
        ];

        $key = "reset:grant:{$token}";
        Redis::setex($key, self::GRANT_TIME * 60, json_encode($grant));

        return $token;
    }

    // Delete/expire OTP in Redis
    public function deleteOtp(array $request, int $userId, bool $deleteOtp): void
    {
        $otpDestination = $this->getOtpDestinationType($request);
        $key = $this->otpKey($userId, $otpDestination);

        if ($deleteOtp) {
            Redis::del($key);
        } else {
            // Mark as expired quickly (similar to setting expire_at = now())
            if (Redis::exists($key)) {
                // set a 1-second TTL
                $payload = $this->readOtpPayload($userId, $otpDestination) ?? [];
                Redis::setex($key, 1, json_encode($payload));
            }
        }
    }
}
