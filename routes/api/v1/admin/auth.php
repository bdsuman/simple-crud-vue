<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
Route::get('user-details', [AuthController::class, 'me'])->name('auth.me');
Route::post('update-profile', [AuthController::class, 'updateProfile'])->name('auth.update-profile');
