<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductoController extends Controller
{
    /**
     * GET /api/v1/admin/productos
     * Parámetros opcionales: ?nombre=&per_page=
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Producto::with('administrador');

        if ($nombre = $request->query('nombre')) {
            $query->where('nom_productos', 'like', "%{$nombre}%");
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        return ProductoResource::collection($query->orderBy('nom_productos')->paginate($perPage));
    }

    public function store(StoreProductoRequest $request): ProductoResource
    {
        $producto = Producto::create($request->validated());
        return new ProductoResource($producto->load('administrador'));
    }

    public function show(Producto $producto): ProductoResource
    {
        $producto->load('administrador.sucursal');
        return new ProductoResource($producto);
    }

    public function update(UpdateProductoRequest $request, Producto $producto): ProductoResource
    {
        $producto->update($request->validated());
        return new ProductoResource($producto->load('administrador'));
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete();
        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}
