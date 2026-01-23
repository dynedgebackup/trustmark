<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\Api\EmailTokenController;
use App\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// SSO API Routes (for Project 1 to notify about logout)
Route::prefix('sso')->group(function () {
    Route::post('/check-session', [SsoController::class, 'checkSession']);
    Route::post('/force-logout', [SsoController::class, 'forceLogout']);
});

// Email Token API Routes
Route::prefix('email-tokens')->group(function () {
    Route::post('/check-countdown', [EmailTokenController::class, 'checkCountdown']);
    Route::post('/time-until-next', [EmailTokenController::class, 'getTimeUntilNext']);
});

Route::any('/payment/webhook', [WebhookController::class, 'handle'])->withoutMiddleware(['throttle:tlpe-webhook']);

// php artisan queue:work --queue=default --memory=128


