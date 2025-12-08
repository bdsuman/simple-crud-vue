<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users/profile-questions', [UserController::class, 'getProfileQuestion'])->name('users.profile.question');
Route::put('/users/profile-questions', [UserController::class, 'updateProfileQuestion'])->name('update.users.profile.question');
Route::get('users/roles', [UserController::class, 'getUserRoles']);
Route::get('users/status', [UserController::class, 'getUserStatus']);
Route::apiResource('users', UserController::class);
