<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthClienteController;
use App\Http\Controllers\Api\AuthAdministradorController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\AdministradorController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\PeliculaController;
use App\Http\Controllers\Api\SalaController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\AsientoController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\OrdenDulceriaController;
use App\Http\Controllers\Api\ProductoController;

/*
|--------------------------------------------------------------------------
| API Routes — CinePlus  (prefijo: /api/v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ═══════════════════════════════════
    //  RUTAS PÚBLICAS (sin autenticación)
    // ═══════════════════════════════════

    // Auth Clientes
    Route::prefix('auth/cliente')->group(function () {
        Route::post('register', [AuthClienteController::class, 'register']);
        Route::post('login',    [AuthClienteController::class, 'login']);
    });

    // Auth Administradores
    Route::prefix('auth/admin')->group(function () {
        Route::post('login', [AuthAdministradorController::class, 'login']);
    });

    // Catálogo público (solo lectura)
    Route::get('peliculas',                          [PeliculaController::class,  'index']);
    Route::get('peliculas/{pelicula}',               [PeliculaController::class,  'show']);
    Route::get('peliculas/{pelicula}/horarios',      [PeliculaController::class,  'horarios']);
    Route::get('peliculas/{pelicula}/categorias',    [PeliculaController::class,  'categorias']);
    Route::get('horarios',                           [HorarioController::class,   'index']);
    Route::get('horarios/{horario}',                 [HorarioController::class,   'show']);
    Route::get('categorias',                         [CategoriaController::class, 'index']);
    Route::get('categorias/{categoria}',             [CategoriaController::class, 'show']);
    Route::get('sucursales',                         [SucursalController::class,  'index']);
    Route::get('sucursales/{sucursal}',              [SucursalController::class,  'show']);
    Route::get('sucursales/{sucursal}/salas',        [SucursalController::class,  'salas']);
    Route::get('salas/{sala}/asientos',              [SalaController::class,      'asientos']);


    // ═════════════════════════════════════════
    //  RUTAS PROTEGIDAS — CLIENTE (sanctum)
    // ═════════════════════════════════════════
    Route::middleware(['auth:sanctum', 'throttle:cliente'])->prefix('cliente')->name('api.cliente.')->group(function () {
        Route::get('me',      [AuthClienteController::class, 'me']);
        Route::post('logout', [AuthClienteController::class, 'logout']);

        // Mis reservas
        Route::get('reservas',               [ReservaController::class, 'index']);
        Route::post('reservas',              [ReservaController::class, 'store']);
        Route::get('reservas/{reserva}',     [ReservaController::class, 'show']);
        Route::put('reservas/{reserva}',     [ReservaController::class, 'update']);
        Route::delete('reservas/{reserva}',  [ReservaController::class, 'destroy']);

        // Órdenes de dulcería
        Route::post('ordenes',           [OrdenDulceriaController::class, 'store']);
        Route::get('ordenes/{orden}',    [OrdenDulceriaController::class, 'show']);
    });


    // ═════════════════════════════════════════
    //  RUTAS PROTEGIDAS — ADMIN (sanctum)
    // ═════════════════════════════════════════
    Route::middleware(['auth:sanctum', 'throttle:admin'])->prefix('admin')->name('api.admin.')->group(function () {
        Route::get('me',      [AuthAdministradorController::class, 'me']);
        Route::post('logout', [AuthAdministradorController::class, 'logout']);

        // Gestión de sucursales
        Route::apiResource('sucursales', SucursalController::class)
            ->parameters(['sucursales' => 'sucursal'])
            ->except(['index', 'show']);

        // Gestión de salas
        Route::apiResource('salas', SalaController::class)
            ->parameters(['salas' => 'sala']);

        // Gestión de asientos
        Route::apiResource('asientos', AsientoController::class)
            ->parameters(['asientos' => 'asiento']);

        // Gestión de películas
        Route::apiResource('peliculas', PeliculaController::class)
            ->parameters(['peliculas' => 'pelicula'])
            ->except(['index', 'show']);

        // Gestión de horarios
        Route::apiResource('horarios', HorarioController::class)
            ->parameters(['horarios' => 'horario'])
            ->except(['index', 'show']);

        // Gestión de categorías
        Route::apiResource('categorias', CategoriaController::class)
            ->parameters(['categorias' => 'categoria'])
            ->except(['index', 'show']);

        // Gestión de productos (dulcería)
        Route::apiResource('productos', ProductoController::class)
            ->parameters(['productos' => 'producto']);

        // Gestión de clientes
        Route::apiResource('clientes', ClienteController::class)
            ->parameters(['clientes' => 'cliente']);

        // Gestión de administradores
        Route::apiResource('administradores', AdministradorController::class)
            ->parameters(['administradores' => 'administrador']);

        // Reservas (vista admin)
        Route::apiResource('reservas', ReservaController::class)
            ->parameters(['reservas' => 'reserva']);

        // Órdenes (vista admin)
        Route::apiResource('ordenes', OrdenDulceriaController::class)
            ->parameters(['ordenes' => 'orden']);

        // Extras anidados
        Route::get('sucursales/{sucursal}/administradores', [SucursalController::class, 'administradores']);
        Route::get('clientes/{cliente}/reservas',           [ClienteController::class,  'reservas']);
        Route::get('reservas/{reserva}/asientos',           [ReservaController::class,  'asientos']);
        Route::get('reservas/{reserva}/ordenes',            [ReservaController::class,  'ordenes']);
    });
});
