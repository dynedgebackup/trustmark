<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\Api\EmailTokenController;
use App\Http\Controllers\Api\WebhookApiController;

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

Route::any('/payment/webhook', [WebhookApiController::class, 'handle'])->withoutMiddleware(['throttle:tlpe-webhook']);

/*.env
QUEUE_CONNECTION=database

php artisan config:clear
php artisan cache:clear

cd /home/projects/trustmark
php artisan queue:table
php artisan migrate

sudo nano /etc/supervisor/conf.d/trustmark-queue.conf

[program:trustmark-queue]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /home/projects/trustmark/artisan queue:work database --sleep=3 --tries=5 --timeout=120
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/home/projects/trustmark/storage/logs/queue-worker.log
stopwaitsecs=3600


sudo chown -R www-data:www-data /home/projects/trustmark
sudo chmod -R 775 /home/projects/trustmark/storage
sudo chmod -R 775 /home/projects/trustmark/bootstrap/cache


sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start trustmark-queue:*

sudo supervisorctl status*/



