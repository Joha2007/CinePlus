<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCliente
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('cliente')) {
            return redirect()->route('cliente.login')
                ->with('error', 'Debes iniciar sesión para continuar.');
        }
        return $next($request);
    }
}
