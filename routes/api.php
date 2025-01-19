<?php

use App\Http\Controllers\Api\AccountDepositController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\PackageSubscribesController;
use App\Http\Controllers\Api\UserSubscribeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthenticationController::class, 'register']);
        Route::post('login', [AuthenticationController::class, 'login']);
        Route::get('logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
    });

    Route::middleware('auth:sanctum')->prefix('package-subscribes')->group(function () {
        Route::get('list', [PackageSubscribesController::class, 'list']);
        Route::post('create', [PackageSubscribesController::class, 'create']);
        Route::get('show/{id}', [PackageSubscribesController::class, 'show']);
        Route::post('update/{id}', [PackageSubscribesController::class, 'update']);
        Route::post('destroy/{id}', [PackageSubscribesController::class, 'destroy']);
    });

    Route::middleware('auth:sanctum')->prefix('user-subscribes')->group(function () {
        Route::get('list', [UserSubscribeController::class, 'list']);
        Route::post('create', [UserSubscribeController::class, 'create']);
        Route::get('show/{id}', [UserSubscribeController::class, 'show']);
    });

    Route::middleware('auth:sanctum')->prefix('account')->group(function () {
        Route::get('deposit', [AccountDepositController::class, 'index']);
        Route::post('deposit', [AccountDepositController::class, 'deposit']);
    });
});


