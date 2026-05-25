<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeliculaRequest;
use App\Http\Requests\UpdatePeliculaRequest;
use App\Http\Resources\PeliculaResource;
use App\Http\Resources\HorarioResource;
use App\Http\Resources\CategoriaResource;
use App\Models\Pelicula;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PeliculaController extends Controller
{
    /**
     * GET /api/v1/peliculas
     * Parámetros opcionales: ?titulo=&rango_edad=&categoria=&per_page=
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Pelicula::with('categorias');

        if ($titulo = $request->query('titulo')) {
            $query->where('nom_pelicula', 'like', "%{$titulo}%");
        }

        if ($rangoEdad = $request->query('rango_edad')) {
            $query->where('rango_edad', $rangoEdad);
        }

        if ($categoria = $request->query('categoria')) {
            $query->whereHas('categorias', fn ($q) => $q->where('nom_categoria', 'like', "%{$categoria}%"));
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        return PeliculaResource::collection($query->orderBy('nom_pelicula')->paginate($perPage));
    }

    public function store(StorePeliculaRequest $request): PeliculaResource
    {
        $data = $request->validated();
        $categorias = $data['categorias'] ?? [];
        unset($data['categorias']);

        $pelicula = Pelicula::create($data);

        if (!empty($categorias)) {
            $pelicula->categorias()->sync($categorias);
        }

        return new PeliculaResource($pelicula->load('categorias'));
    }

    public function show(Pelicula $pelicula): PeliculaResource
    {
        $pelicula->load(['categorias', 'horarios.sala.sucursal']);
        return new PeliculaResource($pelicula);
    }

    public function update(UpdatePeliculaRequest $request, Pelicula $pelicula): PeliculaResource
    {
        $data = $request->validated();
        $categorias = $data['categorias'] ?? null;
        unset($data['categorias']);

        $pelicula->update($data);

        if (!is_null($categorias)) {
            $pelicula->categorias()->sync($categorias);
        }

        return new PeliculaResource($pelicula->load('categorias'));
    }

    public function destroy(Pelicula $pelicula): JsonResponse
    {
        $pelicula->delete();
        return response()->json(['message' => 'Película eliminada correctamente']);
    }

    // GET /api/v1/peliculas/{pelicula}/horarios
    public function horarios(Pelicula $pelicula): AnonymousResourceCollection
    {
        return HorarioResource::collection(
            $pelicula->horarios()->with('sala.sucursal')->get()
        );
    }

    // GET /api/v1/peliculas/{pelicula}/categorias
    public function categorias(Pelicula $pelicula): AnonymousResourceCollection
    {
        return CategoriaResource::collection($pelicula->categorias);
    }
}
