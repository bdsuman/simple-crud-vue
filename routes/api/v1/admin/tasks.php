<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Task\TaskController;

Route::apiResource('tasks', TaskController::class);
Route::put('/tasks/publish/{task}', [TaskController::class, 'togglePublish'])->name('tasks.publish');
