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

        // Estadísticas filtradas por la sucursal del administrador
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

    /**
     * Almacena una nueva película en la base de datos.
     * Valida los campos del formulario, sube la imagen al almacenamiento
     * y asocia las categorías seleccionadas.
     */
    public function peliculasStore(Request $request): RedirectResponse
    {
        // Validación de los campos del formulario de película
        $data = $request->validate([
            'nom_pelicula' => 'required|string|max:150',                     // Nombre obligatorio, máximo 150 caracteres
            'descripcion'  => 'required|string',                             // Sinopsis/descripción obligatoria
            'duracion'     => 'required|integer|min:1',                      // Duración en minutos, mínimo 1 minuto
            'img'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Imagen opcional, formatos permitidos, máximo 5 MB
            'rango_edad'   => 'required|in:TP,+7,+13,+18',                  // Clasificación de edad obligatoria (TP, +7, +13 o +18)
            'categorias'   => 'nullable|array',                              // Lista de categorías opcional
            'categorias.*' => 'exists:categorias,id_categoria',              // Cada ID de categoría debe existir en la tabla categorias
        ]);

        // Separar las categorías del resto de datos antes de crear la película
        $categorias = $data['categorias'] ?? [];
        unset($data['categorias']);
        // Eliminar img de $data validado (puede venir como UploadedFile o null)
        unset($data['img']);

        // Subir imagen solo si se proporcionó un archivo válido
        if ($request->hasFile('img')) {
            $path = Storage::disk('public')->putFile('peliculas', $request->file('img'));
            if ($path) {
                $data['img'] = $path; // Guarda: "peliculas/nombre_hash.ext"
            }
        }

        // Crear la película y sincronizar sus categorías
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

    /**
     * Actualiza los datos de una película existente.
     * Si se sube una nueva imagen, elimina la anterior del almacenamiento.
     * Sincroniza las categorías con los valores enviados en el formulario.
     */
    public function peliculasUpdate(Request $request, int $id): RedirectResponse
    {
        // Buscar la película o lanzar error 404 si no existe
        $pelicula = Pelicula::findOrFail($id);

        // Validación de los campos del formulario de edición
        $data = $request->validate([
            'nom_pelicula' => 'required|string|max:150',                     // Nombre obligatorio, máximo 150 caracteres
            'descripcion'  => 'required|string',                             // Sinopsis/descripción obligatoria
            'duracion'     => 'required|integer|min:1',                      // Duración en minutos, mínimo 1 minuto
            'img'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Imagen opcional, formatos permitidos, máximo 5 MB
            'rango_edad'   => 'required|in:TP,+7,+13,+18',                  // Clasificación de edad (TP, +7, +13 o +18)
            'categorias'   => 'nullable|array',                              // Lista de categorías opcional
            'categorias.*' => 'exists:categorias,id_categoria',              // Cada ID debe existir en la tabla categorias
        ]);

        // Separar las categorías antes de actualizar la película
        $categorias = $data['categorias'] ?? [];
        unset($data['categorias']);
        // Eliminar img de $data validado para no sobreescribir accidentalmente con null
        unset($data['img']);

        // Reemplazar imagen solo si se subió una nueva — si no, se conserva la existente
        if ($request->hasFile('img')) {
            // Eliminar imagen anterior del disco si existe
            if ($pelicula->img) {
                Storage::disk('public')->delete($pelicula->img);
            }
            $path = Storage::disk('public')->putFile('peliculas', $request->file('img'));
            if ($path) {
                $data['img'] = $path;
            }
        }
        // Si NO se subió imagen, $data no contiene 'img' → update no toca ese campo ✓

        // Actualizar los datos de la película y sus categorías
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

    /**
     * Almacena un nuevo horario de función en la base de datos.
     * Valida campos básicos, verifica que la fecha no sea pasada
     * y comprueba que no haya solapamiento con otra función en la misma sala.
     */
    public function horariosStore(Request $request): RedirectResponse
    {
        // Validación de los campos del formulario de horario
        $data = $request->validate([
            'id_pelicula1' => 'required|exists:peliculas,id_pelicula',    // Película obligatoria, debe existir en BD
            'id_sala2'     => 'required|exists:salas,id_sala',            // Sala obligatoria, debe existir en BD
            'hora_inicio'  => 'required|date_format:H:i',                 // Hora en formato HH:MM (24h)
            'fecha'        => 'required|date|after_or_equal:today',       // Fecha obligatoria, no puede ser en el pasado
            'tec_proyecc'  => 'required|in:2D,3D,IMAX',                  // Solo 2D, 3D o IMAX
        ]);

        // Verificar que la sala pertenece a la sucursal del admin
        $sala = Sala::findOrFail($data['id_sala2']);
        if ($sala->id_suc2 !== $this->adminSucId()) {
            return back()->withErrors(['id_sala2' => 'No puedes programar funciones en salas de otra sucursal.'])->withInput();
        }

        // Verificar que no se solape con otra función ya programada en la misma sala y fecha
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

    /**
     * Actualiza los datos de un horario de función existente.
     * Verifica solapamiento excluyendo el propio horario que se está editando.
     */
    public function horariosUpdate(Request $request, int $id): RedirectResponse
    {
        // Buscar el horario o lanzar error 404 si no existe
        $horario = Horario::findOrFail($id);

        // Validación de los campos del formulario de edición
        $data = $request->validate([
            'id_pelicula1' => 'required|exists:peliculas,id_pelicula', // Película obligatoria, debe existir en BD
            'id_sala2'     => 'required|exists:salas,id_sala',         // Sala obligatoria, debe existir en BD
            'hora_inicio'  => 'required|date_format:H:i',              // Hora en formato HH:MM (24h)
            'fecha'        => 'required|date',                         // Fecha obligatoria (permite fechas pasadas al editar)
            'tec_proyecc'  => 'required|in:2D,3D,IMAX',               // Solo 2D, 3D o IMAX
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

    /**
     * Verifica si un nuevo horario se solapa con funciones ya programadas en la misma sala y fecha.
     * Usa la duración de cada película para calcular la hora de fin de cada función.
     *
     * @param  int         $salaId          ID de la sala a verificar
     * @param  string      $fecha           Fecha de la función (Y-m-d)
     * @param  string      $horaInicio      Hora de inicio del nuevo horario (H:i)
     * @param  int         $peliculaId      ID de la película nueva (para calcular su duración)
     * @param  int|null    $excludeId       ID del horario a excluir (para edición)
     * @return string|null Mensaje de conflicto o null si no hay solapamiento
     */
    private function checkHorarioConflicto(
        int $salaId, string $fecha, string $horaInicio, int $peliculaId, ?int $excludeId = null
    ): ?string {
        // Obtener la duración de la nueva película para calcular su hora de fin
        $pelicula    = Pelicula::findOrFail($peliculaId);
        $nuevoInicio = Carbon::createFromFormat('H:i', $horaInicio);
        $nuevoFin    = $nuevoInicio->copy()->addMinutes($pelicula->duracion);

        // Traer todos los horarios de esa sala y fecha (excepto el que se está editando)
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

        return null; // Sin conflicto
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

    /**
     * Lista las salas de la sucursal del administrador autenticado.
     */
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

    /**
     * Muestra el formulario para crear una nueva sala.
     * Solo muestra la sucursal propia del administrador.
     */
    public function salasCreate(): View
    {
        // El admin solo puede crear salas en su propia sucursal
        $sucursales = Sucursal::where('id_suc', $this->adminSucId())->get();
        return view('admin.salas.form', compact('sucursales'));
    }

    /**
     * Almacena la nueva sala y genera automáticamente sus asientos.
     * Los asientos se crean en filas (A, B, C…) con el número indicado por fila.
     */
    public function salasStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_suc2'           => 'required|exists:sucursales,id_suc',  // Sucursal obligatoria, debe existir
            'num_sala'          => 'required|integer|min:1',             // Número de sala obligatorio
            'filas'             => 'required|integer|min:1|max:26',      // Filas A–Z, máximo 26
            'asientos_por_fila' => 'required|integer|min:1|max:30',      // Asientos por fila, máximo 30
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

        $filas      = (int) $data['filas'];
        $asPorFila  = (int) $data['asientos_por_fila'];
        $capacidad  = $filas * $asPorFila;

        // Crear la sala con la capacidad total calculada
        $sala = Sala::create([
            'id_suc2'     => $data['id_suc2'],
            'num_sala'    => $data['num_sala'],
            'capaci_sala' => $capacidad,
        ]);

        // Generar todos los asientos: filas A, B, C… × asientos 1, 2, 3…
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

    /**
     * Muestra el formulario para editar una sala existente.
     * Solo permite editar salas de la propia sucursal.
     * Detecta si tiene reservas activas para bloquear el cambio de capacidad.
     */
    public function salasEdit(int $id): View
    {
        $sala = Sala::with(['sucursal', 'asientos'])
            ->where('id_suc2', $this->adminSucId()) // Solo salas de la propia sucursal
            ->findOrFail($id);
        // El select de sucursal solo muestra la sucursal del admin
        $sucursales = Sucursal::where('id_suc', $this->adminSucId())->get();

        // Detectar la distribución actual de asientos para mostrarla en el formulario
        $filasActuales     = $sala->asientos->groupBy('num_fila')->count();
        $asPorFilaActual   = $sala->asientos->groupBy('num_fila')->first()?->count() ?? 0;

        // Verificar si hay reservas confirmadas que impiden modificar la capacidad
        $tieneReservas = DB::table('reserva_asiento')
            ->join('asientos', 'reserva_asiento.id_asiento1', '=', 'asientos.id_asiento')
            ->join('reservas', 'reserva_asiento.id_reserva1', '=', 'reservas.id_reserva')
            ->where('asientos.id_sala1', $sala->id_sala)
            ->where('reservas.estado', 'Confirmada')
            ->exists();

        return view('admin.salas.form', compact('sala', 'sucursales', 'tieneReservas', 'filasActuales', 'asPorFilaActual'));
    }

    /**
     * Actualiza los datos de una sala.
     * Si se cambia la capacidad y no hay reservas activas, regenera todos los asientos.
     */
    public function salasUpdate(Request $request, int $id): RedirectResponse
    {
        // Solo permite actualizar salas de la propia sucursal
        $sala = Sala::with('asientos')
            ->where('id_suc2', $this->adminSucId())
            ->findOrFail($id);

        $cambiarCapacidad = $request->filled('filas') && $request->filled('asientos_por_fila');

        // Reglas base; se añaden filas/asientos_por_fila solo si se envían
        $rules = [
            'id_suc2'  => 'required|exists:sucursales,id_suc', // Sucursal obligatoria
            'num_sala' => 'required|integer|min:1',             // Número de sala obligatorio
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

            // Eliminar los asientos actuales y regenerar con la nueva configuración
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

    /**
     * Elimina una sala de la propia sucursal. Solo se permite si no tiene horarios programados.
     * La eliminación en cascada borra también todos sus asientos.
     */
    public function salasDestroy(int $id): RedirectResponse
    {
        // Solo permite eliminar salas de la propia sucursal
        $sala = Sala::where('id_suc2', $this->adminSucId())->findOrFail($id);

        // Bloquear si tiene horarios (activos o históricos) programados
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

    /**
     * Almacena un nuevo producto de dulcería en la base de datos.
     * Asocia el producto al administrador que lo crea y sube la imagen si se proporcionó.
     */
    public function productosStore(Request $request): RedirectResponse
    {
        // Validación de los campos del formulario de producto
        $data = $request->validate([
            'nom_productos'   => 'required|string|max:100',                      // Nombre del producto obligatorio, máximo 100 caracteres
            'descripcion'     => 'nullable|string|max:500',                      // Descripción opcional, máximo 500 caracteres
            'precio_producto' => 'required|numeric|min:0.01',                    // Precio obligatorio, mínimo $0.01
            'stock'           => 'required|integer|min:0',                       // Cantidad en stock obligatoria, mínimo 0 unidades
            'img'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Imagen opcional, formatos permitidos, máximo 5 MB
        ]);

        // Asignar el administrador autenticado como propietario del producto
        $data['id_admin1'] = session('admin')['id_admin'];
        // Eliminar img del array validado (puede ser UploadedFile o null)
        unset($data['img']);

        // Subir imagen solo si se proporcionó un archivo válido
        if ($request->hasFile('img')) {
            $path = Storage::disk('public')->putFile('productos', $request->file('img'));
            if ($path) {
                $data['img'] = $path; // Guarda: "productos/nombre_hash.ext"
            }
        }

        // Crear el producto en la base de datos
        Producto::create($data);
        return redirect()->route('admin.productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function productosEdit(int $id): View
    {
        $producto = Producto::findOrFail($id);
        return view('admin.productos.form', compact('producto'));
    }

    /**
     * Actualiza los datos de un producto de dulcería existente.
     * Si se sube una nueva imagen, elimina la anterior del almacenamiento.
     */
    public function productosUpdate(Request $request, int $id): RedirectResponse
    {
        // Buscar el producto o lanzar error 404 si no existe
        $producto = Producto::findOrFail($id);

        // Validación de los campos del formulario de edición de producto
        $data = $request->validate([
            'nom_productos'   => 'required|string|max:100',                      // Nombre del producto obligatorio, máximo 100 caracteres
            'descripcion'     => 'nullable|string|max:500',                      // Descripción opcional, máximo 500 caracteres
            'precio_producto' => 'required|numeric|min:0.01',                    // Precio obligatorio, mínimo $0.01
            'stock'           => 'required|integer|min:0',                       // Cantidad en stock obligatoria, mínimo 0 unidades
            'img'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Imagen opcional, formatos permitidos, máximo 5 MB
        ]);

        // Eliminar img del array validado para no sobreescribir con null
        unset($data['img']);

        // Reemplazar imagen solo si se subió una nueva — si no, se conserva la existente
        if ($request->hasFile('img')) {
            // Eliminar imagen anterior si existe
            if ($producto->img) {
                Storage::disk('public')->delete($producto->img);
            }
            $path = Storage::disk('public')->putFile('productos', $request->file('img'));
            if ($path) {
                $data['img'] = $path;
            }
        }
        // Si NO se subió imagen, $data no contiene 'img' → update no toca ese campo ✓

        // Actualizar el producto con los nuevos datos
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
