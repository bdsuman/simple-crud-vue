<?php

use App\Models\Expert;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/seed-experts', function () {
    Artisan::call('db:seed', [
        '--class' => 'ExpertSeeder',
        '--force' => true,
    ]);

    return Expert::count();
});

Route::view('{any}', "welcome")->where('any', "^(?!api).*$");

