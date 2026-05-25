<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoriaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CategoriaResource::collection(Categoria::all());
    }

    public function store(Request $request): CategoriaResource
    {
        $request->validate([
            'nom_categoria' => 'required|string|max:50|unique:categorias,nom_categoria',
        ]);

        $categoria = Categoria::create($request->only('nom_categoria'));
        return new CategoriaResource($categoria);
    }

    public function show(Categoria $categoria): CategoriaResource
    {
        return new CategoriaResource($categoria);
    }

    public function update(Request $request, Categoria $categoria): CategoriaResource
    {
        $request->validate([
            'nom_categoria' => 'required|string|max:50|unique:categorias,nom_categoria,' . $categoria->id_categoria . ',id_categoria',
        ]);

        $categoria->update($request->only('nom_categoria'));
        return new CategoriaResource($categoria);
    }

    public function destroy(Categoria $categoria): JsonResponse
    {
        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }
}
