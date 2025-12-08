<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Auth\ResendOtpRequest;
use App\Http\Requests\Mobile\Auth\VerifyOtpRequest;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;

/**
 * @group Auth
 * @subgroup OTP
 * @unauthenticated
 */
class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Verify OTP
     *
     * @param VerifyOtpRequest $request
     * @return JsonResponse
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return $this->otpService->verifyOtp($validated);
    }

    /**
     * Resend OTP
     *
     * @param ResendOtpRequest $request
     * @return JsonResponse
     */
    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return $this->otpService->requestOtp($validated);
    }
}
