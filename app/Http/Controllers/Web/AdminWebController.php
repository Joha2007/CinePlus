<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asiento;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Horario;
use App\Models\Pelicula;
use App\Models\Producto;
use App\Models\Reserva;
use App\Models\Sala;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminWebController extends Controller
{
    // ─── Dashboard ──────────────────────────────

    public function dashboard(): View
    {
        $stats = [
            'peliculas' => Pelicula::count(),
            'reservas'  => Reserva::count(),
            'clientes'  => Cliente::count(),
            'horarios'  => Horario::where('fecha', '>=', now()->toDateString())->count(),
        ];

        $ultimasReservas = Reserva::with(['cliente', 'horario.pelicula'])
            ->latest()
            ->take(6)
            ->get();

        $proximasFunc = Horario::with(['pelicula', 'sala'])
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'ultimasReservas', 'proximasFunc'));
    }

    // ─── Películas ──────────────────────────────

    public function peliculasIndex(): View
    {
        $peliculas = Pelicula::with('categorias')->withCount('horarios')->get();
        return view('admin.peliculas.index', compact('peliculas'));
    }

    public function peliculasCreate(): View
    {
        $categorias = Categoria::all();
        return view('admin.peliculas.form', compact('categorias'));
    }

    public function peliculasStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom_pelicula' => 'required|string|max:150',
            'descripcion'  => 'required|string',
            'duracion'     => 'required|integer|min:1',
            'img'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'rango_edad'   => 'required|in:TP,+7,+13,+18',
            'categorias'   => 'nullable|array',
            'categorias.*' => 'exists:categorias,id_categoria',
        ]);

        $categorias = $data['categorias'] ?? [];
        unset($data['categorias']);

        // Subir imagen si se proporcionó
        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('peliculas', 'public');
        } else {
            unset($data['img']);
        }

        $p = Pelicula::create($data);
        if ($categorias) $p->categorias()->sync($categorias);

        return redirect()->route('admin.peliculas.index')->with('success', 'Película creada correctamente.');
    }

    public function peliculasEdit(int $id): View
    {
        $pelicula   = Pelicula::with('categorias')->findOrFail($id);
        $categorias = Categoria::all();
        return view('admin.peliculas.form', compact('pelicula', 'categorias'));
    }

    public function peliculasUpdate(Request $request, int $id): RedirectResponse
    {
        $pelicula = Pelicula::findOrFail($id);

        $data = $request->validate([
            'nom_pelicula' => 'required|string|max:150',
            'descripcion'  => 'required|string',
            'duracion'     => 'required|integer|min:1',
            'img'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'rango_edad'   => 'required|in:TP,+7,+13,+18',
            'categorias'   => 'nullable|array',
            'categorias.*' => 'exists:categorias,id_categoria',
        ]);

        $categorias = $data['categorias'] ?? [];
        unset($data['categorias']);

        // Subir nueva imagen si se proporcionó, borrar la anterior
        if ($request->hasFile('img')) {
            if ($pelicula->img) {
                Storage::disk('public')->delete($pelicula->img);
            }
            $data['img'] = $request->file('img')->store('peliculas', 'public');
        } else {
            unset($data['img']); // mantener la imagen existente
        }

        $pelicula->update($data);
        $pelicula->categorias()->sync($categorias);

        return redirect()->route('admin.peliculas.index')->with('success', 'Película actualizada correctamente.');
    }

    public function peliculasDestroy(int $id): RedirectResponse
    {
        Pelicula::findOrFail($id)->delete();
        return redirect()->route('admin.peliculas.index')->with('success', 'Película eliminada.');
    }

    // ─── Reservas ───────────────────────────────

    public function reservasIndex(): View
    {
        $reservas = Reserva::with([
                'cliente',
                'horario.pelicula',
                'horario.sala.sucursal',
                'asientos',
                'ordenes.productos',
            ])
            ->latest()
            ->get();

        $stats = [
            'total'      => $reservas->count(),
            'confirmadas'=> $reservas->where('estado', 'Confirmada')->count(),
            'canceladas' => $reservas->where('estado', 'Cancelada')->count(),
            'ingresos'   => $reservas->where('estado', 'Confirmada')->sum('monto'),
        ];

        return view('admin.reservas.index', compact('reservas', 'stats'));
    }

    public function reservasDestroy(int $id): RedirectResponse
    {
        $reserva = Reserva::with(['asientos', 'ordenes.productos'])->findOrFail($id);

        if ($reserva->estado !== 'Cancelada') {
            // Liberar asientos
            $asientoIds = $reserva->asientos->pluck('id_asiento');
            Asiento::whereIn('id_asiento', $asientoIds)->update(['estado' => 'Disponible']);

            // Devolver stock de snacks
            foreach ($reserva->ordenes as $orden) {
                foreach ($orden->productos as $prod) {
                    $prod->increment('stock', $prod->pivot->cantidad);
                }
            }
        }

        $reserva->update(['estado' => 'Cancelada']);
        return redirect()->route('admin.reservas.index')->with('success', 'Reserva cancelada.');
    }

    // ─── Horarios ────────────────────────────────

    public function horariosIndex(): View
    {
        $horarios = Horario::with(['pelicula', 'sala.sucursal'])->orderBy('fecha')->get();
        return view('admin.horarios.index', compact('horarios'));
    }

    public function horariosCreate(): View
    {
        $peliculas = Pelicula::all();
        $salas     = Sala::with('sucursal')->get();
        return view('admin.horarios.form', compact('peliculas', 'salas'));
    }

    public function horariosStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_pelicula1' => 'required|exists:peliculas,id_pelicula',
            'id_sala2'     => 'required|exists:salas,id_sala',
            'hora_inicio'  => 'required|date_format:H:i',
            'fecha'        => 'required|date',
            'tec_proyecc'  => 'required|in:2D,3D,IMAX',
        ]);

        Horario::create($data);
        return redirect()->route('admin.horarios.index')->with('success', 'Horario creado correctamente.');
    }

    public function horariosEdit(int $id): View
    {
        $horario   = Horario::findOrFail($id);
        $peliculas = Pelicula::all();
        $salas     = Sala::with('sucursal')->get();
        return view('admin.horarios.form', compact('horario', 'peliculas', 'salas'));
    }

    public function horariosUpdate(Request $request, int $id): RedirectResponse
    {
        $horario = Horario::findOrFail($id);

        $data = $request->validate([
            'id_pelicula1' => 'required|exists:peliculas,id_pelicula',
            'id_sala2'     => 'required|exists:salas,id_sala',
            'hora_inicio'  => 'required|date_format:H:i',
            'fecha'        => 'required|date',
            'tec_proyecc'  => 'required|in:2D,3D,IMAX',
        ]);

        $horario->update($data);
        return redirect()->route('admin.horarios.index')->with('success', 'Horario actualizado.');
    }

    public function horariosDestroy(int $id): RedirectResponse
    {
        Horario::findOrFail($id)->delete();
        return redirect()->route('admin.horarios.index')->with('success', 'Horario eliminado.');
    }

    // ─── Categorías ─────────────────────────────

    public function categoriasIndex(): View
    {
        $categorias = Categoria::withCount('peliculas')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    // ─── Salas ──────────────────────────────────

    public function salasIndex(): View
    {
        $salas = Sala::with(['sucursal', 'asientos'])->get();
        return view('admin.salas.index', compact('salas'));
    }

    // ─── Productos ──────────────────────────────

    public function productosIndex(): View
    {
        $productos = Producto::with('administrador.sucursal')->get();
        return view('admin.productos.index', compact('productos'));
    }

    public function productosCreate(): View
    {
        return view('admin.productos.form');
    }

    public function productosStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom_productos'   => 'required|string|max:100',
            'descripcion'     => 'nullable|string|max:500',
            'precio_producto' => 'required|numeric|min:0.01',
            'stock'           => 'required|integer|min:0',
            'img'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data['id_admin1'] = session('admin')['id_admin'];

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('productos', 'public');
        } else {
            unset($data['img']);
        }

        Producto::create($data);
        return redirect()->route('admin.productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function productosEdit(int $id): View
    {
        $producto = Producto::findOrFail($id);
        return view('admin.productos.form', compact('producto'));
    }

    public function productosUpdate(Request $request, int $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);

        $data = $request->validate([
            'nom_productos'   => 'required|string|max:100',
            'descripcion'     => 'nullable|string|max:500',
            'precio_producto' => 'required|numeric|min:0.01',
            'stock'           => 'required|integer|min:0',
            'img'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('img')) {
            if ($producto->img) {
                Storage::disk('public')->delete($producto->img);
            }
            $data['img'] = $request->file('img')->store('productos', 'public');
        } else {
            unset($data['img']);
        }

        $producto->update($data);
        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function productosDestroy(int $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);
        if ($producto->img) {
            Storage::disk('public')->delete($producto->img);
        }
        $producto->delete();
        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado.');
    }

    // ─── Clientes ───────────────────────────────

    public function clientesIndex(): View
    {
        $clientes = Cliente::withCount('reservas')->get();
        return view('admin.clientes.index', compact('clientes'));
    }
}
