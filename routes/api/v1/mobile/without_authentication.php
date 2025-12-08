<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\Auth\OtpController;
use App\Http\Controllers\Mobile\Auth\AuthController;
use App\Http\Controllers\Mobile\Auth\RegisterController;
use App\Http\Controllers\Mobile\Auth\ForgotPasswordController;

Route::post('register', [RegisterController::class, 'register'])->name('auth.register');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('verify-otp', [OtpController::class, 'verifyOtp'])->name('auth.verify_otp');
Route::post('resend-otp', [OtpController::class, 'resendOtp'])->name('auth.resend_otp');
Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('auth.forgot_password');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('auth.reset_password');
