<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::post('login', [AuthController::class, 'login'])->name('auth.login');
