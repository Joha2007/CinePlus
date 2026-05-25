<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('admin')) {
            return redirect()->route('admin.login')
                ->with('error', 'Debes iniciar sesión como administrador.');
        }
        return $next($request);
    }
}
