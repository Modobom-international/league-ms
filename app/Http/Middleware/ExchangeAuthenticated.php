<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeAuthenticated
{
    // app/Http/Middleware/AuthExchange.php
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('canstum')->check()) {
            return redirect()->route('exchange.LoginForm'); // route trang login riêng của exchange
        }

        return $next($request);
    }
}

