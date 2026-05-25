<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Si no hay sesión activa de administrador, redirigir al login
        if (! session('admin')) {
            return redirect()->route('admin.login')
                ->with('error', 'Debes iniciar sesión como administrador.');
        }

        // Ejecutar la solicitud y luego agregar cabeceras anti-caché
        $response = $next($request);

        // Estas cabeceras impiden que el navegador guarde en caché las páginas
        // protegidas. Al presionar "atrás" después de cerrar sesión, el
        // navegador no mostrará la página cacheada sino que la recargará,
        // lo que ejecutará este middleware y redirigirá al login.
        return $response
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private')
            ->header('Pragma',        'no-cache')
            ->header('Expires',       'Fri, 01 Jan 1990 00:00:00 GMT');
    }
}
