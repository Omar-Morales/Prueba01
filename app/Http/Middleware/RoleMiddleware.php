<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login'); // Redirige si no está logueado
        }

        // Obtiene el rol del usuario autenticado
        $userRole = Auth::user()->role->name ?? null; // Asegúrate de tener la relación con roles en el modelo User

        // Verifica si el rol del usuario está permitido
        if (!in_array($userRole, $roles)) {
            return abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
