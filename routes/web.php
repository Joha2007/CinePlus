<?php

use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\ClienteWebController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\HorarioWebController;
use App\Http\Controllers\Web\PeliculaWebController;
use App\Http\Controllers\Web\SucursalWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — CinePlus
|--------------------------------------------------------------------------
*/

// ─── Inicio ────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Catálogo público ──────────────────────────────────────
Route::get('/peliculas',         [PeliculaWebController::class, 'index'])->name('peliculas.index');
Route::get('/peliculas/{id}',    [PeliculaWebController::class, 'show'])->name('peliculas.show');
Route::get('/cartelera',         [HorarioWebController::class,  'index'])->name('horarios.index');
Route::get('/sucursales',        [SucursalWebController::class, 'index'])->name('sucursales.index');

// ─── Auth cliente ──────────────────────────────────────────
Route::get ('/login',    [AuthWebController::class, 'showLoginCliente'])->name('cliente.login');
Route::post('/login',    [AuthWebController::class, 'loginCliente'])->name('cliente.login.post');
Route::get ('/registro', [AuthWebController::class, 'showRegisterCliente'])->name('cliente.register');
Route::post('/registro', [AuthWebController::class, 'registerCliente'])->name('cliente.register.post');
Route::post('/logout',   [AuthWebController::class, 'logoutCliente'])->name('cliente.logout');

// ─── Auth admin ────────────────────────────────────────────
Route::get ('/admin/login', [AuthWebController::class, 'showLoginAdmin'])->name('admin.login');
Route::post('/admin/login', [AuthWebController::class, 'loginAdmin'])->name('admin.login.post');
Route::post('/admin/logout',[AuthWebController::class, 'logoutAdmin'])->name('admin.logout');

// ─── Área de cliente (requiere sesión) ─────────────────────
Route::middleware('auth.cliente')->group(function () {
    Route::get  ('/mis-reservas',                  [ClienteWebController::class, 'misReservas'])->name('cliente.reservas');
    Route::get  ('/reservar/{horario}',            [ClienteWebController::class, 'reservar'])->name('reservas.create');
    Route::post ('/reservar',                      [ClienteWebController::class, 'reservarStore'])->name('reservas.store');
    Route::get  ('/reservas/{id}/editar',          [ClienteWebController::class, 'reservasEdit'])->name('reservas.edit');
    Route::put  ('/reservas/{id}',                 [ClienteWebController::class, 'reservasUpdate'])->name('reservas.update');
    Route::post ('/reservas/{id}/cancelar',        [ClienteWebController::class, 'reservasCancelar'])->name('reservas.cancelar');
});

// ─── Panel admin (requiere sesión admin) ───────────────────
Route::middleware('auth.admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('dashboard');

    // Películas
    Route::get   ('/peliculas',            [AdminWebController::class, 'peliculasIndex'])->name('peliculas.index');
    Route::get   ('/peliculas/crear',      [AdminWebController::class, 'peliculasCreate'])->name('peliculas.create');
    Route::post  ('/peliculas',            [AdminWebController::class, 'peliculasStore'])->name('peliculas.store');
    Route::get   ('/peliculas/{id}/editar',[AdminWebController::class, 'peliculasEdit'])->name('peliculas.edit');
    Route::put   ('/peliculas/{id}',       [AdminWebController::class, 'peliculasUpdate'])->name('peliculas.update');
    Route::delete('/peliculas/{id}',       [AdminWebController::class, 'peliculasDestroy'])->name('peliculas.destroy');

    // Horarios
    Route::get   ('/horarios',              [AdminWebController::class, 'horariosIndex'])->name('horarios.index');
    Route::get   ('/horarios/crear',        [AdminWebController::class, 'horariosCreate'])->name('horarios.create');
    Route::post  ('/horarios',              [AdminWebController::class, 'horariosStore'])->name('horarios.store');
    Route::get   ('/horarios/{id}/editar',  [AdminWebController::class, 'horariosEdit'])->name('horarios.edit');
    Route::put   ('/horarios/{id}',         [AdminWebController::class, 'horariosUpdate'])->name('horarios.update');
    Route::delete('/horarios/{id}',         [AdminWebController::class, 'horariosDestroy'])->name('horarios.destroy');

    // Categorías
    Route::get   ('/categorias',       [AdminWebController::class, 'categoriasIndex'])->name('categorias.index');
    Route::post  ('/categorias',       function(\Illuminate\Http\Request $req) {
        $req->validate(['nom_categoria' => 'required|string|max:50|unique:categorias,nom_categoria']);
        \App\Models\Categoria::create($req->only('nom_categoria'));
        return back()->with('success', 'Categoría creada.');
    })->name('categorias.store');
    Route::delete('/categorias/{id}',  function(int $id) {
        \App\Models\Categoria::findOrFail($id)->delete();
        return back()->with('success', 'Categoría eliminada.');
    })->name('categorias.destroy');

    // Salas
    Route::get('/salas', [AdminWebController::class, 'salasIndex'])->name('salas.index');

    // Productos (dulcería) — CRUD completo
    Route::get   ('/productos',              [AdminWebController::class, 'productosIndex'])->name('productos.index');
    Route::get   ('/productos/crear',        [AdminWebController::class, 'productosCreate'])->name('productos.create');
    Route::post  ('/productos',              [AdminWebController::class, 'productosStore'])->name('productos.store');
    Route::get   ('/productos/{id}/editar',  [AdminWebController::class, 'productosEdit'])->name('productos.edit');
    Route::put   ('/productos/{id}',         [AdminWebController::class, 'productosUpdate'])->name('productos.update');
    Route::delete('/productos/{id}',         [AdminWebController::class, 'productosDestroy'])->name('productos.destroy');

    // Clientes
    Route::get('/clientes', [AdminWebController::class, 'clientesIndex'])->name('clientes.index');

    // Reservas
    Route::get   ('/reservas',       [AdminWebController::class, 'reservasIndex'])->name('reservas.index');
    Route::delete('/reservas/{id}',  [AdminWebController::class, 'reservasDestroy'])->name('reservas.destroy');
});
