<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Testimonial\TestimonialController;

Route::apiResource('testimonials', TestimonialController::class);
Route::put('/testimonials/publish/{testimonial}', [TestimonialController::class, 'togglePublish'])->name('testimonials.publish');
