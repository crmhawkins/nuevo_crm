<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug: Log de información
        \Log::info('AdminMiddleware - Verificando acceso', [
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'access_level' => auth()->check() ? auth()->user()->access_level_id : 'N/A',
            'url' => $request->url()
        ]);

        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            \Log::warning('AdminMiddleware - Usuario no autenticado');
            return redirect()->route('login');
        }

        $user = auth()->user();
        \Log::info('AdminMiddleware - Usuario autenticado', [
            'user_id' => $user->id,
            'name' => $user->name,
            'access_level_id' => $user->access_level_id
        ]);

        // Verificar que el usuario tenga nivel de acceso de administrador (nivel 1), gerente (nivel 2), contable (nivel 3) o gestor (nivel 4)
        if ($user->access_level_id != 1 && $user->access_level_id != 2 && $user->access_level_id != 3 && $user->access_level_id != 4) {
            \Log::warning('AdminMiddleware - Acceso denegado', [
                'user_id' => $user->id,
                'access_level_id' => $user->access_level_id,
                'required_levels' => [1, 2, 3, 4]
            ]);
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        \Log::info('AdminMiddleware - Acceso permitido');
        return $next($request);
    }
}
