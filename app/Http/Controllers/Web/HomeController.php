<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pelicula;
use App\Models\Sucursal;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $peliculas  = Pelicula::with('categorias')->latest()->take(8)->get();
        $sucursales = Sucursal::all();

        return view('home', compact('peliculas', 'sucursales'));
    }
}
