<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use Illuminate\View\View;

class SucursalWebController extends Controller
{
    public function index(): View
    {
        $sucursales = Sucursal::with(['salas', 'administradores'])->get();
        return view('sucursales.index', compact('sucursales'));
    }
}
