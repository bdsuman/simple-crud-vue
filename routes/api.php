<?php

use Illuminate\Support\Facades\Route;

// Health Check
if (app()->environment('local', 'staging')) {
    require __DIR__ . '/api/v1/common/health.php';
}

/******************************************For Web App API ******************************************************/

// Without Authentication
require __DIR__ . '/api/v1/admin/without_authentication.php';

Route::middleware(['auth:sanctum'])->group(function () {
    require __DIR__ . '/api/v1/admin/auth.php';
    require __DIR__ . '/api/v1/admin/users.php';
    require __DIR__ . '/api/v1/admin/tasks.php';
});

