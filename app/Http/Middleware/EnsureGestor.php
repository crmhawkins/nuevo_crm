<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureGestor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $allowedUserIds = [73, 75, 81, 83, 160];

        if (
            !$user
            || (
                (int) $user->access_level_id !== 4
                && !in_array((int) $user->id, $allowedUserIds, true)
            )
        ) {
            abort(403);
        }

        return $next($request);
    }
}

