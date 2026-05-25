<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Sucursal;
use Illuminate\View\View;

class HorarioWebController extends Controller
{
    public function index(): View
    {
        $horarios = Horario::with(['pelicula.categorias', 'sala.sucursal'])
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        // Sucursales que tienen al menos una función programada
        $sucursales = Sucursal::whereHas('salas.horarios', function ($q) {
            $q->where('fecha', '>=', now()->toDateString());
        })->get();

        // Horarios agrupados por id de sucursal para los tabs
        $porSucursal = $horarios->groupBy(fn($h) => $h->sala?->id_suc2 ?? 0);

        return view('horarios.index', compact('horarios', 'porSucursal', 'sucursales'));
    }
}
