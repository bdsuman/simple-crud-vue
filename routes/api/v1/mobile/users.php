<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\Users\UserController;
use App\Http\Controllers\Mobile\Users\CreateUserProfileController;

Route::prefix('user')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/password', [UserController::class, 'changePassword'])->name('user.password.update');
    Route::delete('/account', [UserController::class, 'deleteAccount'])->name('user.account.delete');
    Route::post('/setup-profile', [CreateUserProfileController::class, '__invoke'])->name('user.profile.create');
    Route::get('/setup/attributes', [UserController::class, 'getOptionsData'])->name('user.setup.attributes');
    Route::post('/voice', [UserController::class, 'uploadVoice'])->name('user.voice.create');
    Route::delete('/voice', [UserController::class, 'deleteVoice'])->name('user.voice.remove');
    Route::get('/fcm', [UserController::class, 'triggerNotification'])->name('user.fcm.trigger');
});
