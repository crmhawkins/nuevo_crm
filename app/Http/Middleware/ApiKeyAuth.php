<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');
        $validApiKey = 'xTNsxKKK15cE';

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'API key inválida o no proporcionada'
            ], 401);
        }

        return $next($request);
    }
}

