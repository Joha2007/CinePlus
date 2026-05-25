<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeliculaRequest;
use App\Http\Requests\UpdatePeliculaRequest;
use App\Models\Pelicula;
use Illuminate\Http\JsonResponse;

class PeliculaController extends Controller
{
    public function index(): JsonResponse
    {
        $peliculas = Pelicula::with('categorias')->get();
        return response()->json($peliculas);
    }

    public function store(StorePeliculaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $categorias = $data['categorias'] ?? [];
        unset($data['categorias']);

        $pelicula = Pelicula::create($data);

        if (!empty($categorias)) {
            $pelicula->categorias()->sync($categorias);
        }

        return response()->json($pelicula->load('categorias'), 201);
    }

    public function show(Pelicula $pelicula): JsonResponse
    {
        $pelicula->load(['categorias', 'horarios.sala.sucursal']);
        return response()->json($pelicula);
    }

    public function update(UpdatePeliculaRequest $request, Pelicula $pelicula): JsonResponse
    {
        $data = $request->validated();
        $categorias = $data['categorias'] ?? null;
        unset($data['categorias']);

        $pelicula->update($data);

        if (!is_null($categorias)) {
            $pelicula->categorias()->sync($categorias);
        }

        return response()->json($pelicula->load('categorias'));
    }

    public function destroy(Pelicula $pelicula): JsonResponse
    {
        $pelicula->delete();
        return response()->json(['message' => 'Película eliminada correctamente']);
    }

    // GET /api/peliculas/{pelicula}/horarios
    public function horarios(Pelicula $pelicula): JsonResponse
    {
        return response()->json($pelicula->horarios()->with('sala.sucursal')->get());
    }

    // GET /api/peliculas/{pelicula}/categorias
    public function categorias(Pelicula $pelicula): JsonResponse
    {
        return response()->json($pelicula->categorias);
    }
}
