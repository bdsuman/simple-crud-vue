<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\Task\TaskController;

Route::apiResource('tasks', TaskController::class)->only(['index', 'show']);
