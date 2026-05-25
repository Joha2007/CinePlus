<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeliculaWebController extends Controller
{
    public function index(Request $request): View
    {
        $categorias = Categoria::all();

        $query = Pelicula::with('categorias');

        if ($request->filled('categoria')) {
            $query->whereHas('categorias', fn($q) =>
                $q->where('categorias.id_categoria', $request->categoria)
            );
        }

        $peliculas = $query->get();

        return view('peliculas.index', compact('peliculas', 'categorias'));
    }

    public function show(int $id): View
    {
        $pelicula = Pelicula::with([
            'categorias',
            'horarios.sala.sucursal',
        ])->findOrFail($id);

        return view('peliculas.show', compact('pelicula'));
    }
}
