<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('user-details', [AuthController::class, 'me'])->name('auth.me');
Route::put('update-profile', [AuthController::class, 'updateProfile'])->name('auth.update-profile');
Route::put('change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
