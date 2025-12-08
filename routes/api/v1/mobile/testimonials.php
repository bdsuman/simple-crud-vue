<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\Testimonial\TestimonialController;

Route::apiResource('testimonials', TestimonialController::class)->only(['index', 'show']);
