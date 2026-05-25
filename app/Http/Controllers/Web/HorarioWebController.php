<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use Illuminate\View\View;

class HorarioWebController extends Controller
{
    public function index(): View
    {
        $horarios = Horario::with(['pelicula', 'sala.sucursal'])
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return view('horarios.index', compact('horarios'));
    }
}
