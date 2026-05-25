<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asiento;
use App\Models\Horario;
use App\Models\OrdenDulceria;
use App\Models\OrdenProducto;
use App\Models\Producto;
use App\Models\Reserva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ClienteWebController extends Controller
{
    /**
     * Muestra las reservas del cliente autenticado.
     */
    public function misReservas(): View
    {
        $clienteId = session('cliente')['id_cliente'];

        $reservas = Reserva::with([
                'horario.pelicula',
                'horario.sala.sucursal',
                'asientos',
                'ordenes.productos',
            ])
            ->where('id_cliente1', $clienteId)
            ->latest()
            ->get();

        return view('cliente.reservas', compact('reservas'));
    }

    /**
     * Muestra la pantalla de selección de asientos para un horario.
     */
    public function reservar(int $horario): View
    {
        $horario = Horario::with([
                'pelicula',
                'sala.sucursal',
                'sala.asientos',
            ])
            ->findOrFail($horario);

        // Asientos agrupados por fila
        $asientos = $horario->sala->asientos->sortBy('num_asiento')->groupBy('num_fila');

        // Productos de dulcería disponibles
        $productos = Producto::where('stock', '>', 0)->get();

        return view('cliente.reservar', compact('horario', 'asientos', 'productos'));
    }

    /**
     * Muestra el formulario de edición de una reserva existente.
     */
    public function reservasEdit(int $id): View
    {
        $clienteId = session('cliente')['id_cliente'];

        $reserva = Reserva::with([
                'horario.pelicula',
                'horario.sala.sucursal',
                'horario.sala.asientos',
                'asientos',
                'ordenes.productos',
            ])
            ->where('id_cliente1', $clienteId)
            ->where('estado', 'Confirmada')
            ->findOrFail($id);

        // Asientos de la sala agrupados por fila
        $asientos = $reserva->horario->sala->asientos
            ->sortBy('num_asiento')
            ->groupBy('num_fila');

        // IDs de los asientos que ya tiene esta reserva (para marcarlos pre-seleccionados)
        $asientosActuales = $reserva->asientos->pluck('id_asiento');

        // Cantidades actuales de snacks: [ id_producto => cantidad ]
        $snacksActuales = collect();
        foreach ($reserva->ordenes as $orden) {
            foreach ($orden->productos as $prod) {
                $snacksActuales[$prod->id_producto] = $prod->pivot->cantidad;
            }
        }

        // Productos disponibles + los que ya tiene en la orden (aunque stock=0)
        $productos = Producto::where('stock', '>', 0)
            ->orWhereIn('id_producto', $snacksActuales->keys())
            ->get();

        return view('cliente.editar-reserva', compact(
            'reserva', 'asientos', 'asientosActuales', 'snacksActuales', 'productos'
        ));
    }

    /**
     * Actualiza una reserva existente (cambia asientos y/o snacks).
     */
    public function reservasUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'asientos'    => 'required|array|min:1',
            'asientos.*'  => 'exists:asientos,id_asiento',
            'metodo_pago' => 'required|in:Tarjeta,Efectivo,Transferencia',
            'snacks'      => 'nullable|array',
            'snacks.*'    => 'integer|min:0|max:20',
        ]);

        $clienteId = session('cliente')['id_cliente'];

        $reserva = Reserva::with(['asientos', 'ordenes.productos'])
            ->where('id_cliente1', $clienteId)
            ->where('estado', 'Confirmada')
            ->findOrFail($id);

        $horario        = Horario::findOrFail($reserva->id_horario1);
        $asientosNuevos = $request->asientos;
        $asientosViejos = $reserva->asientos->pluck('id_asiento')->toArray();

        $aLiberar = array_diff($asientosViejos, $asientosNuevos); // se van a liberar
        $aNuevos  = array_diff($asientosNuevos, $asientosViejos); // recién seleccionados

        // Verificar que los asientos recién elegidos sigan disponibles
        if (!empty($aNuevos)) {
            $disponibles = Asiento::whereIn('id_asiento', $aNuevos)
                ->where('id_sala1', $horario->id_sala2)
                ->where('estado', 'Disponible')
                ->count();
            if ($disponibles !== count($aNuevos)) {
                return back()->withErrors(['asientos' => 'Uno o más asientos ya no están disponibles.'])->withInput();
            }
        }

        // ── Calcular snacks nuevos ───────────────────────────────
        $snacksSolicitados = collect($request->snacks ?? [])
            ->filter(fn($qty) => (int) $qty > 0)
            ->mapWithKeys(fn($qty, $id) => [$id => (int) $qty]);

        $montoSnacks = 0;
        $cantSnacks  = 0;
        $productosSnack = collect();

        // Cantidades anteriores para calcular diferencia de stock
        $snacksAnteriores = collect();
        foreach ($reserva->ordenes as $orden) {
            foreach ($orden->productos as $prod) {
                $snacksAnteriores[$prod->id_producto] = $prod->pivot->cantidad;
            }
        }

        if ($snacksSolicitados->isNotEmpty()) {
            $productosSnack = Producto::whereIn('id_producto', $snacksSolicitados->keys())->get();

            foreach ($productosSnack as $prod) {
                $cantNueva  = $snacksSolicitados[$prod->id_producto];
                $cantVieja  = $snacksAnteriores[$prod->id_producto] ?? 0;
                $diferencia = $cantNueva - $cantVieja; // >0 = necesita más stock

                if ($diferencia > 0 && $prod->stock < $diferencia) {
                    return back()->withErrors([
                        'snacks' => "Stock insuficiente para \"{$prod->nom_productos}\" (disponible: {$prod->stock}).",
                    ])->withInput();
                }
                $montoSnacks += $prod->precio_producto * $cantNueva;
                $cantSnacks  += $cantNueva;
            }
        }

        $precioPorAsiento = 5.00;
        $monto = ($precioPorAsiento * count($asientosNuevos)) + $montoSnacks;

        // ── Actualizar asientos ──────────────────────────────────
        if (!empty($aLiberar)) {
            Asiento::whereIn('id_asiento', $aLiberar)->update(['estado' => 'Disponible']);
        }
        if (!empty($aNuevos)) {
            Asiento::whereIn('id_asiento', $aNuevos)->update(['estado' => 'Ocupado']);
        }
        $reserva->asientos()->sync($asientosNuevos);

        // ── Reemplazar órdenes de dulcería ───────────────────────
        foreach ($reserva->ordenes as $orden) {
            // Devolver stock de lo que tenía antes
            foreach ($orden->productos as $prod) {
                $prod->increment('stock', $prod->pivot->cantidad);
            }
            $orden->productos()->detach();
            $orden->delete();
        }

        if ($snacksSolicitados->isNotEmpty()) {
            $ordenNueva = OrdenDulceria::create([
                'id_reserva2' => $reserva->id_reserva,
                'total'       => $montoSnacks,
                'cant_produc' => $cantSnacks,
            ]);

            foreach ($productosSnack as $prod) {
                $cantidad = $snacksSolicitados[$prod->id_producto];
                OrdenProducto::create([
                    'id_orden1'    => $ordenNueva->id_orden,
                    'id_producto1' => $prod->id_producto,
                    'cantidad'     => $cantidad,
                ]);
                $prod->decrement('stock', $cantidad);
            }
        }

        // ── Actualizar datos de la reserva ───────────────────────
        $reserva->update([
            'metodo_pago' => $request->metodo_pago,
            'monto'       => $monto,
        ]);

        return redirect()
            ->route('cliente.reservas')
            ->with('success', '¡Reserva actualizada! Código: ' . $reserva->num_confirmacion);
    }

    /**
     * Cancela una reserva: libera asientos y devuelve stock de snacks.
     */
    public function reservasCancelar(int $id): RedirectResponse
    {
        $clienteId = session('cliente')['id_cliente'];

        $reserva = Reserva::with(['asientos', 'ordenes.productos'])
            ->where('id_cliente1', $clienteId)
            ->where('estado', 'Confirmada')
            ->findOrFail($id);

        // Liberar asientos
        $asientoIds = $reserva->asientos->pluck('id_asiento');
        Asiento::whereIn('id_asiento', $asientoIds)->update(['estado' => 'Disponible']);

        // Devolver stock de snacks
        foreach ($reserva->ordenes as $orden) {
            foreach ($orden->productos as $prod) {
                $prod->increment('stock', $prod->pivot->cantidad);
            }
        }

        $reserva->update(['estado' => 'Cancelada']);

        return back()->with('success', 'Reserva ' . $reserva->num_confirmacion . ' cancelada correctamente.');
    }

    /**
     * Procesa la reserva (store).
     */
    public function reservarStore(Request $request): RedirectResponse
    {
        $request->validate([
            'id_horario'   => 'required|exists:horarios,id_horario',
            'asientos'     => 'required|array|min:1',
            'asientos.*'   => 'exists:asientos,id_asiento',
            'metodo_pago'  => 'required|in:Tarjeta,Efectivo,Transferencia',
            'snacks'       => 'nullable|array',
            'snacks.*'     => 'integer|min:0|max:20',
        ]);

        $clienteId  = session('cliente')['id_cliente'];
        $horarioId  = $request->id_horario;
        $asientoIds = $request->asientos;

        // Verificar que los asientos siguen disponibles
        $asientos = Asiento::whereIn('id_asiento', $asientoIds)
            ->where('id_sala1', Horario::findOrFail($horarioId)->id_sala2)
            ->where('estado', 'Disponible')
            ->get();

        if ($asientos->count() !== count($asientoIds)) {
            return back()->withErrors(['asientos' => 'Uno o más asientos ya no están disponibles.']);
        }

        // ── Calcular snacks ──────────────────────────────────────
        $snacksSolicitados = collect($request->snacks ?? [])
            ->filter(fn($qty) => (int) $qty > 0)
            ->mapWithKeys(fn($qty, $id) => [$id => (int) $qty]);

        $montoSnacks = 0;
        $cantSnacks  = 0;
        $productosSnack = collect();

        if ($snacksSolicitados->isNotEmpty()) {
            $productosSnack = Producto::whereIn('id_producto', $snacksSolicitados->keys())->get();

            foreach ($productosSnack as $prod) {
                $cantidad = $snacksSolicitados[$prod->id_producto];
                if ($prod->stock < $cantidad) {
                    return back()->withErrors([
                        'snacks' => "Stock insuficiente para \"{$prod->nom_productos}\" (disponible: {$prod->stock}).",
                    ])->withInput();
                }
                $montoSnacks += $prod->precio_producto * $cantidad;
                $cantSnacks  += $cantidad;
            }
        }

        // ── Precio por asiento: $5.00 ────────────────────────────
        $precioPorAsiento = 5.00;
        $monto = ($precioPorAsiento * count($asientoIds)) + $montoSnacks;

        // ── Crear la reserva ─────────────────────────────────────
        $reserva = Reserva::create([
            'id_cliente1'      => $clienteId,
            'id_horario1'      => $horarioId,
            'fecha_compra'     => now()->toDateString(),
            'metodo_pago'      => $request->metodo_pago,
            'monto'            => $monto,
            'estado'           => 'Confirmada',
            'num_confirmacion' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        // Sincronizar asientos y marcarlos como Ocupados
        $reserva->asientos()->sync($asientoIds);
        Asiento::whereIn('id_asiento', $asientoIds)->update(['estado' => 'Ocupado']);

        // ── Crear orden de dulcería si hay snacks ─────────────────
        if ($snacksSolicitados->isNotEmpty()) {
            $orden = OrdenDulceria::create([
                'id_reserva2' => $reserva->id_reserva,
                'total'       => $montoSnacks,
                'cant_produc' => $cantSnacks,
            ]);

            foreach ($productosSnack as $prod) {
                $cantidad = $snacksSolicitados[$prod->id_producto];

                OrdenProducto::create([
                    'id_orden1'    => $orden->id_orden,
                    'id_producto1' => $prod->id_producto,
                    'cantidad'     => $cantidad,
                ]);

                // Descontar stock
                $prod->decrement('stock', $cantidad);
            }
        }

        return redirect()
            ->route('cliente.reservas')
            ->with('success', '¡Reserva confirmada! Tu código: ' . $reserva->num_confirmacion);
    }
}
