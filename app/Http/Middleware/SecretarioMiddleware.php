<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Secretario;

class SecretarioMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !($user instanceof Secretario)) {
            return response()->json(['message' => 'Acesso restrito ao Secretário.'], 403);
        }

        return $next($request);
    }
}
