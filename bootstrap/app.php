<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.cliente' => \App\Http\Middleware\AuthCliente::class,
            'auth.admin'   => \App\Http\Middleware\AuthAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Cuando cualquier guard lanza AuthenticationException (ej: auth:sanctum),
        // redirigir al login del cliente en lugar del inexistente route('login').
        $exceptions->render(function (
            \Illuminate\Auth\AuthenticationException $_e,
            \Illuminate\Http\Request $request
        ) {
            if (! $request->expectsJson()) {
                return redirect(route('cliente.login'));
            }
        });
    })->create();
