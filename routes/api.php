<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthTokenController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserBaseDataController;

Route::prefix('v1')->group(function () {
    Route::post('auth/token', [AuthTokenController::class, 'token']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthTokenController::class, 'logout']);
        Route::get('me', [AuthTokenController::class, 'me']);

        Route::middleware('permission:users.read')->get('users', [UserController::class, 'index']);
        Route::middleware('permission:users.read')->get('users/{user}', [UserController::class, 'show']);
        Route::middleware('permission:users.write')->post('users', [UserController::class, 'store']);
        Route::middleware('permission:users.write')->put('users/{user}', [UserController::class, 'update']);
        Route::middleware('permission:users.write')->delete('users/{user}', [UserController::class, 'destroy']);

        Route::middleware('permission:user_basedata.read')->get('user-basedata', [UserBaseDataController::class, 'index']);
        Route::middleware('permission:user_basedata.read')->get('user-basedata/{user_basedatum}', [UserBaseDataController::class, 'show']);
        Route::middleware('permission:user_basedata.write')->post('user-basedata', [UserBaseDataController::class, 'store']);
        Route::middleware('permission:user_basedata.write')->put('user-basedata/{user_basedatum}', [UserBaseDataController::class, 'update']);
        Route::middleware('permission:user_basedata.write')->delete('user-basedata/{user_basedatum}', [UserBaseDataController::class, 'destroy']);
    });
});
