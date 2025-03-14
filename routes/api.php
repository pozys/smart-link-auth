<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(static function () {
    Route::controller(AuthController::class)->group(static function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });
});

Route::middleware('api',)->group(static function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(static function () {
            Route::post('logout', 'logout');
            Route::post('verify-token', 'verifyToken');
        });
    });
});
