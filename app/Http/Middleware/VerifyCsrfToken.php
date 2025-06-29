<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/midtrans/webhook',     // Dengan slash awal
        'midtrans/webhook',      // Tanpa slash awal
        'midtrans/webhook/*',    // Wildcard
        '/midtrans/webhook/*',   // Wildcard dengan slash
    ];
}