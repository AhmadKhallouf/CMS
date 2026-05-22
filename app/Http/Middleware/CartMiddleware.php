<?php

namespace App\Http\Middleware;

use App\Services\CartManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CartMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\Http\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('login', 'logout')) {
            return $next($request);
        }

        $cart = app(CartManager::class);

        if ($user = $request->user()) {
            $cart->syncForAuthenticatedUser($user);
        }

        return $next($request);
    }
}
