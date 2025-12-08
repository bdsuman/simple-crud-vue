<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;

// Health Check
if (app()->environment('local', 'staging')) {
    require __DIR__ . '/api/v1/common/health.php';
}

/******************************************For Web App API ******************************************************/

// Without Authentication
require __DIR__ . '/api/v1/admin/without_authentication.php';

// Media
// Route::post('/upload-media', [MediaController::class, 'upload']);

Route::middleware(['auth:sanctum'])->group(function () {
    require __DIR__ . '/api/v1/admin/auth.php';
    require __DIR__ . '/api/v1/admin/users.php';
    require __DIR__ . '/api/v1/admin/tasks.php';
});

/******************************************For Mobile App API ******************************************************/

Route::prefix('mobile')->as('mobile.')->group(function () {

    // Without Authentication
    require __DIR__ . '/api/v1/mobile/without_authentication.php';

    Route::middleware(['auth:sanctum'])->group(function () {
        require __DIR__ . '/api/v1/mobile/auth.php';
        require __DIR__ . '/api/v1/mobile/users.php';
        require __DIR__ . '/api/v1/mobile/tasks.php';
    });
});
