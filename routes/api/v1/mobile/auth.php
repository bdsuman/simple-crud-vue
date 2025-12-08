<?php

use App\Http\Controllers\Mobile\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
