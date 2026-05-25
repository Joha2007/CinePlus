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
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminWebController extends Controller
{
    // ─── Helper: ID de la sucursal del admin autenticado ───────

    private function adminSucId(): int
    {
        return (int) session('admin')['id_suc'];
    }

    // ─── Dashboard ──────────────────────────────

    public function dashboard(): View
    {
        $sucId = $this->adminSucId();

        $stats = [
            'peliculas' => Pelicula::count(),   // Películas son globales (catálogo compartido)
            'reservas'  => Reserva::whereHas('horario.sala', fn($q) => $q->where('id_suc2', $sucId))->count(),
            'clientes'  => Cliente::count(),
            'horarios'  => Horario::whereHas('sala', fn($q) => $q->where('id_suc2', $sucId))
                               ->where('fecha', '>=', now()->toDateString())->count(),
        ];

        $ultimasReservas = Reserva::with(['cliente', 'horario.pelicula'])
            ->whereHas('horario.sala', fn($q) => $q->where('id_suc2', $sucId))
            ->latest()
            ->take(6)
            ->get();

        $proximasFunc = Horario::with(['pelicula', 'sala'])
            ->whereHas('sala', fn($q) => $q->where('id_suc2', $sucId))
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
        unset($data['img']);

        if ($request->hasFile('img')) {
            $path = Storage::disk('public')->putFile('peliculas', $request->file('img'));
            if ($path) {
                $data['img'] = $path;
            }
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
        unset($data['img']);

        if ($request->hasFile('img')) {
            if ($pelicula->img) {
                Storage::disk('public')->delete($pelicula->img);
            }
            $path = Storage::disk('public')->putFile('peliculas', $request->file('img'));
            if ($path) {
                $data['img'] = $path;
            }
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
        $sucId = $this->adminSucId();

        // Solo reservas de funciones en salas de la sucursal del admin
        $reservas = Reserva::with([
                'cliente',
                'horario.pelicula',
                'horario.sala.sucursal',
                'asientos',
                'ordenes.productos',
            ])
            ->whereHas('horario.sala', fn($q) => $q->where('id_suc2', $sucId))
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

    // ─── Horarios ────────────────────────────────

    public function horariosIndex(): View
    {
        $sucId = $this->adminSucId();

        // Solo horarios de salas de la sucursal del admin
        $horarios = Horario::with(['pelicula', 'sala.sucursal'])
            ->whereHas('sala', fn($q) => $q->where('id_suc2', $sucId))
            ->orderBy('fecha')
            ->get();

        return view('admin.horarios.index', compact('horarios'));
    }

    public function horariosCreate(): View
    {
        $peliculas = Pelicula::all();
        // Solo salas de la sucursal del admin
        $salas = Sala::with('sucursal')->where('id_suc2', $this->adminSucId())->get();
        return view('admin.horarios.form', compact('peliculas', 'salas'));
    }

    public function horariosStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_pelicula1' => 'required|exists:peliculas,id_pelicula',
            'id_sala2'     => 'required|exists:salas,id_sala',
            'hora_inicio'  => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $hora = Carbon::createFromFormat('H:i', $value);
                    if ($hora->lt(Carbon::createFromFormat('H:i', '09:00')) ||
                        $hora->gt(Carbon::createFromFormat('H:i', '22:00'))) {
                        $fail('La hora de inicio debe estar entre las 9:00 AM y las 10:00 PM.');
                    }
                },
            ],
            'fecha'        => 'required|date|after_or_equal:today',
            'tec_proyecc'  => 'required|in:2D,3D,IMAX',
        ]);

        // Verificar que la sala pertenece a la sucursal del admin
        $sala = Sala::findOrFail($data['id_sala2']);
        if ($sala->id_suc2 !== $this->adminSucId()) {
            return back()->withErrors(['id_sala2' => 'No puedes programar funciones en salas de otra sucursal.'])->withInput();
        }

        // Verificar que no se solape con otra función en la misma sala y fecha
        $conflicto = $this->checkHorarioConflicto(
            $data['id_sala2'], $data['fecha'], $data['hora_inicio'], $data['id_pelicula1']
        );
        if ($conflicto) {
            return back()->withErrors(['hora_inicio' => $conflicto])->withInput();
        }

        Horario::create($data);
        return redirect()->route('admin.horarios.index')->with('success', 'Horario creado correctamente.');
    }

    public function horariosEdit(int $id): View
    {
        $horario   = Horario::findOrFail($id);
        $peliculas = Pelicula::all();
        // Solo salas de la sucursal del admin
        $salas = Sala::with('sucursal')->where('id_suc2', $this->adminSucId())->get();
        return view('admin.horarios.form', compact('horario', 'peliculas', 'salas'));
    }

    public function horariosUpdate(Request $request, int $id): RedirectResponse
    {
        $horario = Horario::findOrFail($id);

        $data = $request->validate([
            'id_pelicula1' => 'required|exists:peliculas,id_pelicula',
            'id_sala2'     => 'required|exists:salas,id_sala',
            'hora_inicio'  => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $hora = Carbon::createFromFormat('H:i', $value);
                    if ($hora->lt(Carbon::createFromFormat('H:i', '09:00')) ||
                        $hora->gt(Carbon::createFromFormat('H:i', '22:00'))) {
                        $fail('La hora de inicio debe estar entre las 9:00 AM y las 10:00 PM.');
                    }
                },
            ],
            'fecha'        => 'required|date',
            'tec_proyecc'  => 'required|in:2D,3D,IMAX',
        ]);

        // Verificar que la sala pertenece a la sucursal del admin
        $sala = Sala::findOrFail($data['id_sala2']);
        if ($sala->id_suc2 !== $this->adminSucId()) {
            return back()->withErrors(['id_sala2' => 'No puedes programar funciones en salas de otra sucursal.'])->withInput();
        }

        // Verificar solapamiento (excluye el propio horario que se está editando)
        $conflicto = $this->checkHorarioConflicto(
            $data['id_sala2'], $data['fecha'], $data['hora_inicio'], $data['id_pelicula1'], $id
        );
        if ($conflicto) {
            return back()->withErrors(['hora_inicio' => $conflicto])->withInput();
        }

        $horario->update($data);
        return redirect()->route('admin.horarios.index')->with('success', 'Horario actualizado.');
    }

    private function checkHorarioConflicto(
        int $salaId, string $fecha, string $horaInicio, int $peliculaId, ?int $excludeId = null
    ): ?string {
        $pelicula    = Pelicula::findOrFail($peliculaId);
        $nuevoInicio = Carbon::createFromFormat('H:i', $horaInicio);
        $nuevoFin    = $nuevoInicio->copy()->addMinutes($pelicula->duracion);

        $horariosExistentes = Horario::with('pelicula')
            ->where('id_sala2', $salaId)
            ->where('fecha', $fecha)
            ->when($excludeId, fn ($q) => $q->where('id_horario', '!=', $excludeId))
            ->get();

        foreach ($horariosExistentes as $h) {
            $existInicio = Carbon::createFromFormat('H:i:s', $h->hora_inicio);
            $existFin    = $existInicio->copy()->addMinutes($h->pelicula->duracion);

            // Hay solapamiento si: nuevo empieza antes de que el existente termine
            //                   Y nuevo termina después de que el existente empiece
            if ($nuevoInicio->lt($existFin) && $nuevoFin->gt($existInicio)) {
                return 'La sala ya tiene programada "'
                    . $h->pelicula->nom_pelicula . '" de '
                    . $existInicio->format('h:i A') . ' a ' . $existFin->format('h:i A')
                    . '. El nuevo horario (' . $nuevoInicio->format('h:i A')
                    . ' – ' . $nuevoFin->format('h:i A') . ') se solapa con esa función.';
            }
        }

        return null;
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
        $sucId = $this->adminSucId();
        $salas = Sala::withCount(['asientos', 'horarios'])
            ->with('sucursal')
            ->where('id_suc2', $sucId)
            ->orderBy('num_sala')
            ->get();
        return view('admin.salas.index', compact('salas'));
    }

    public function salasCreate(): View
    {
        $sucursales = Sucursal::where('id_suc', $this->adminSucId())->get();
        return view('admin.salas.form', compact('sucursales'));
    }

    public function salasStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_suc2'           => 'required|exists:sucursales,id_suc',
            'num_sala'          => 'required|integer|min:1',
            'filas'             => 'required|integer|min:1|max:26',
            'asientos_por_fila' => 'required|integer|min:1|max:30',
        ]);

        // Verificar que la sucursal sea la propia del admin
        if ((int) $data['id_suc2'] !== $this->adminSucId()) {
            return back()->withErrors(['id_suc2' => 'No puedes gestionar salas de otra sucursal.'])->withInput();
        }

        // Verificar que no exista otra sala con el mismo número en esta sucursal
        $duplicado = Sala::where('id_suc2', $data['id_suc2'])
            ->where('num_sala', $data['num_sala'])
            ->exists();
        if ($duplicado) {
            return back()
                ->withErrors(['num_sala' => 'Ya existe una sala con ese número en esta sucursal.'])
                ->withInput();
        }

        $filas     = (int) $data['filas'];
        $asPorFila = (int) $data['asientos_por_fila'];
        $capacidad = $filas * $asPorFila;

        $sala = Sala::create([
            'id_suc2'     => $data['id_suc2'],
            'num_sala'    => $data['num_sala'],
            'capaci_sala' => $capacidad,
        ]);

        // Generar asientos: filas A, B, C… × asientos 1, 2, 3…
        $letras   = range('A', 'Z');
        $asientos = [];
        for ($f = 0; $f < $filas; $f++) {
            for ($a = 1; $a <= $asPorFila; $a++) {
                $asientos[] = [
                    'id_sala1'    => $sala->id_sala,
                    'num_fila'    => $letras[$f],
                    'num_asiento' => $a,
                    'estado'      => 'Disponible',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }
        Asiento::insert($asientos);

        return redirect()->route('admin.salas.index')
            ->with('success', "Sala {$sala->num_sala} creada con {$capacidad} asientos ({$filas} filas × {$asPorFila} asientos).");
    }

    public function salasEdit(int $id): View
    {
        $sala = Sala::with(['sucursal', 'asientos'])
            ->where('id_suc2', $this->adminSucId()) // Solo salas de la propia sucursal
            ->findOrFail($id);
        $sucursales = Sucursal::where('id_suc', $this->adminSucId())->get();

        $filasActuales   = $sala->asientos->groupBy('num_fila')->count();
        $asPorFilaActual = $sala->asientos->groupBy('num_fila')->first()?->count() ?? 0;

        // Verificar si hay reservas confirmadas que impiden modificar la capacidad
        $tieneReservas = DB::table('reserva_asiento')
            ->join('asientos', 'reserva_asiento.id_asiento1', '=', 'asientos.id_asiento')
            ->join('reservas', 'reserva_asiento.id_reserva1', '=', 'reservas.id_reserva')
            ->where('asientos.id_sala1', $sala->id_sala)
            ->where('reservas.estado', 'Confirmada')
            ->exists();

        return view('admin.salas.form', compact('sala', 'sucursales', 'tieneReservas', 'filasActuales', 'asPorFilaActual'));
    }

    public function salasUpdate(Request $request, int $id): RedirectResponse
    {
        $sala = Sala::with('asientos')
            ->where('id_suc2', $this->adminSucId())
            ->findOrFail($id);

        $cambiarCapacidad = $request->filled('filas') && $request->filled('asientos_por_fila');

        // Reglas base; se añaden filas/asientos_por_fila solo si se envían
        $rules = [
            'id_suc2'  => 'required|exists:sucursales,id_suc',
            'num_sala' => 'required|integer|min:1',
        ];
        if ($cambiarCapacidad) {
            $rules['filas']             = 'required|integer|min:1|max:26';
            $rules['asientos_por_fila'] = 'required|integer|min:1|max:30';
        }

        $data = $request->validate($rules);

        // Verificar que no exista otra sala con el mismo número en esta sucursal
        $duplicado = Sala::where('id_suc2', $data['id_suc2'])
            ->where('num_sala', $data['num_sala'])
            ->where('id_sala', '!=', $sala->id_sala)
            ->exists();
        if ($duplicado) {
            return back()
                ->withErrors(['num_sala' => 'Ya existe una sala con ese número en esta sucursal.'])
                ->withInput();
        }

        if ($cambiarCapacidad) {
            // Bloquear si hay reservas confirmadas en esta sala
            $tieneReservas = DB::table('reserva_asiento')
                ->join('asientos', 'reserva_asiento.id_asiento1', '=', 'asientos.id_asiento')
                ->join('reservas', 'reserva_asiento.id_reserva1', '=', 'reservas.id_reserva')
                ->where('asientos.id_sala1', $sala->id_sala)
                ->where('reservas.estado', 'Confirmada')
                ->exists();

            if ($tieneReservas) {
                return back()
                    ->withErrors(['filas' => 'No se puede modificar la capacidad: hay reservas activas en esta sala.'])
                    ->withInput();
            }

            $filas     = (int) $data['filas'];
            $asPorFila = (int) $data['asientos_por_fila'];
            $capacidad = $filas * $asPorFila;

            $sala->asientos()->delete();

            $letras         = range('A', 'Z');
            $nuevosAsientos = [];
            for ($f = 0; $f < $filas; $f++) {
                for ($a = 1; $a <= $asPorFila; $a++) {
                    $nuevosAsientos[] = [
                        'id_sala1'    => $sala->id_sala,
                        'num_fila'    => $letras[$f],
                        'num_asiento' => $a,
                        'estado'      => 'Disponible',
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
            Asiento::insert($nuevosAsientos);

            $sala->update([
                'id_suc2'     => $data['id_suc2'],
                'num_sala'    => $data['num_sala'],
                'capaci_sala' => $capacidad,
            ]);

            return redirect()->route('admin.salas.index')
                ->with('success', "Sala {$sala->num_sala} actualizada con nueva capacidad: {$capacidad} asientos.");
        }

        // Solo actualizar número de sala y sucursal sin tocar asientos
        $sala->update([
            'id_suc2'  => $data['id_suc2'],
            'num_sala' => $data['num_sala'],
        ]);

        return redirect()->route('admin.salas.index')->with('success', 'Sala actualizada correctamente.');
    }

    public function salasDestroy(int $id): RedirectResponse
    {
        $sala = Sala::where('id_suc2', $this->adminSucId())->findOrFail($id);

        // Bloquear si tiene horarios programados
        $tieneHorarios = Horario::where('id_sala2', $sala->id_sala)->exists();
        if ($tieneHorarios) {
            return redirect()->route('admin.salas.index')
                ->with('error', 'No se puede eliminar la Sala ' . $sala->num_sala . ' porque tiene horarios programados. Elimina primero los horarios.');
        }

        $num = $sala->num_sala;
        $sala->delete(); // Cascade elimina asientos automáticamente

        return redirect()->route('admin.salas.index')
            ->with('success', "Sala {$num} eliminada correctamente.");
    }

    // ─── Productos ──────────────────────────────

    public function productosIndex(): View
    {
        // Solo productos registrados por este administrador
        $adminId   = session('admin')['id_admin'];
        $productos = Producto::with('administrador.sucursal')
            ->where('id_admin1', $adminId)
            ->get();
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
        unset($data['img']);

        if ($request->hasFile('img')) {
            $path = Storage::disk('public')->putFile('productos', $request->file('img'));
            if ($path) {
                $data['img'] = $path;
            }
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

        unset($data['img']);

        if ($request->hasFile('img')) {
            if ($producto->img) {
                Storage::disk('public')->delete($producto->img);
            }
            $path = Storage::disk('public')->putFile('productos', $request->file('img'));
            if ($path) {
                $data['img'] = $path;
            }
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
