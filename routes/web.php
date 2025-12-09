<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', function () {
    echo config('app.name');
});

Route::get('/login', function () {
    return response()->json([
        'message' => __('app.login_required')
    ], Response::HTTP_UNAUTHORIZED);
})->name('login');
