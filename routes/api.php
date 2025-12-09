<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebserverController;
use App\Http\Controllers\VhostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user/login', [AuthController::class, 'loginAction'])->name('user.login');
Route::post('/user/register', [AuthController::class, 'registerAction'])->name('user.register');

Route::middleware(['auth:sanctum', 'throttle:webserver'])->controller(WebserverController::class)->group(function() {
    Route::get('/webserver/start', 'start');
    Route::get('/webserver/stop', 'stop');
    Route::get('/webserver/restart', 'restart');
    Route::get('/webserver/reload', 'reload');
});

Route::middleware(['auth:sanctum', 'throttle:vhost'])->controller(VhostController::class)->group(function() {
    Route::get('/vhosts', 'index');
    Route::get('/vhost/{id}', 'get');
    Route::post('/vhost/create', 'create');
    Route::delete('/vhost/delete', 'delete');
});
