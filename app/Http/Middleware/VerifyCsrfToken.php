<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/updatePaymentResponse',
        '/application/updatePaymentResponse',
        '/application/public/updatePaymentResponse',
        '/payment/webhook',
        '/application/payment/webhook',
        'application/payment/webhook',
        'payment/webhook',
        '/api/sso/force-logout',
    ];
}
