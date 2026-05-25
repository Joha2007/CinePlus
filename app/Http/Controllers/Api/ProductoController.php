<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    public function index(): JsonResponse
    {
        $productos = Producto::with('administrador')->get();
        return response()->json($productos);
    }

    public function store(StoreProductoRequest $request): JsonResponse
    {
        $producto = Producto::create($request->validated());
        return response()->json($producto->load('administrador'), 201);
    }

    public function show(Producto $producto): JsonResponse
    {
        $producto->load('administrador.sucursal');
        return response()->json($producto);
    }

    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $producto->update($request->validated());
        return response()->json($producto->load('administrador'));
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete();
        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}
