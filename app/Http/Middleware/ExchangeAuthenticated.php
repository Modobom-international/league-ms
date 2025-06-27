<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeAuthenticated
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('canstum')->check()) {
            if ($request->expectsJson()) {
                // Nếu là request AJAX / fetch
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            // Nếu là truy cập bằng trình duyệt thì redirect về trang login
            return redirect()->route('exchange.LoginForm');
        }

        return $next($request);
    }
}

