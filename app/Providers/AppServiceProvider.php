<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sanctum puede autenticar tanto Clientes como Administradores
        \Laravel\Sanctum\Sanctum::usePersonalAccessTokenModel(
            \Laravel\Sanctum\PersonalAccessToken::class
        );

        // Rate limiters para la API REST
        RateLimiter::for('cliente', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id_cliente ?: $request->ip());
        });

        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(120)
                ->by($request->user()?->id_admin ?: $request->ip());
        });
    }
}
