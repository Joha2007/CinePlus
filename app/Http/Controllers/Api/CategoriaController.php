<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(): JsonResponse
    {
        $categorias = Categoria::with('peliculas')->get();
        return response()->json($categorias);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom_categoria' => 'required|string|max:50|unique:categorias,nom_categoria',
        ]);

        $categoria = Categoria::create($request->only('nom_categoria'));
        return response()->json($categoria, 201);
    }

    public function show(Categoria $categoria): JsonResponse
    {
        $categoria->load('peliculas');
        return response()->json($categoria);
    }

    public function update(Request $request, Categoria $categoria): JsonResponse
    {
        $request->validate([
            'nom_categoria' => 'required|string|max:50|unique:categorias,nom_categoria,' . $categoria->id_categoria . ',id_categoria',
        ]);

        $categoria->update($request->only('nom_categoria'));
        return response()->json($categoria);
    }

    public function destroy(Categoria $categoria): JsonResponse
    {
        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }
}
