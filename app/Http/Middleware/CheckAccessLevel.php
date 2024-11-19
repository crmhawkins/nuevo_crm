<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccessLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $level
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $level)
    {
        // Verificar si el usuario está autenticado
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Por favor, inicia sesión.');
        }

        // Verificar el nivel de acceso del usuario
        if ($request->user()->access_level_id > $level) {
            return redirect()->route('dashboard')->with('toast',[
                'icon' => 'error',
                'mensaje' => 'No tienes permisos para acceder a esta página.'
            ]);
        }

        return $next($request);
    }
}
