<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Apis\ApiController;




Route::any('/', [ApiController::class, 'index']);
Route::prefix('agent')->group(function () {
    Route::post('login', [ApiController::class, 'login']);
    Route::middleware('auth:agent')->group(function () {
        Route::post('profile', [ApiController::class, 'profile']);
        Route::post('logout', [ApiController::class, 'logout']);
    });
});
