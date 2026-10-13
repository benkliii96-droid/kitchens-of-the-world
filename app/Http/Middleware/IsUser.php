<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden for you'], 403);
        }

        return $next($request);
    }
}
