<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ThemePreference
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Verifica si el usuario está autenticado
        if (Auth::check()) {
            // Convierte el valor de is_dark a booleano
            $isDarkMode = (bool) Auth::user()->is_dark;
            //dd($isDarkMode);
        } else {
            // Valor por defecto si el usuario no está autenticado
            $isDarkMode = false;
        }

        // Comparte la preferencia del tema con todas las vistas
        view()->share('isDarkMode', $isDarkMode);

        return $next($request);
    }
}
