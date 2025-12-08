<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RegisterController;

Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('register', [RegisterController::class, 'register'])->name('auth.register');
