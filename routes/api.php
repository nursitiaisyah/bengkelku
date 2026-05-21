<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ServiceTypeController;
use App\Http\Controllers\Api\V1\ServiceTypeImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('service-types/options', [ServiceTypeController::class, 'options']);
        Route::post('service-types/{id}/image', [ServiceTypeImageController::class, 'store']);
        Route::apiResource('service-types', ServiceTypeController::class);
    });
});
