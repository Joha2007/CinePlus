@extends('layouts.admin')

@section('title', 'Reservas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Reservas</h3>
        <small class="text-secondary">Gestión de todas las reservas</small>
    </div>
</div>

{{-- Estadísticas rápidas --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card-admin p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:rgba(99,102,241,.15);border-radius:10px;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-ticket-detailed text-primary fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                    <small class="text-secondary">Total reservas</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-admin p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:rgba(34,197,94,.15);border-radius:10px;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-check-circle text-success fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ $stats['confirmadas'] }}</div>
                    <small class="text-secondary">Confirmadas</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-admin p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:rgba(229,9,20,.15);border-radius:10px;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-x-circle text-danger fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ $stats['canceladas'] }}</div>
                    <small class="text-secondary">Canceladas</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-admin p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:rgba(234,179,8,.15);border-radius:10px;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-currency-dollar text-warning fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">${{ number_format($stats['ingresos'], 2) }}</div>
                    <small class="text-secondary">Ingresos</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card-admin p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-secondary mb-1">Buscar</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text" style="background:#333;border-color:#444">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" id="filtroTexto" class="form-control"
                       placeholder="Código, cliente, película...">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-secondary mb-1">Estado</label>
            <select id="filtroEstado" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="Confirmada">Confirmadas</option>
                <option value="Cancelada">Canceladas</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-secondary mb-1">Pago</label>
            <select id="filtroPago" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary btn-sm w-100" onclick="limpiarFiltros()">
                <i class="bi bi-x-lg me-1"></i>Limpiar
            </button>
        </div>
    </div>
</div>

<div class="card-admin p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaReservas">
            <thead>
                <tr>
                    <th class="ps-4">Código</th>
                    <th>Cliente</th>
                    <th>Película / Función</th>
                    <th>Asientos</th>
                    <th>Monto</th>
                    <th>Pago</th>
                    <th>Estado</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservas as $r)
                <tr data-estado="{{ $r->estado }}"
                    data-pago="{{ $r->metodo_pago }}"
                    data-texto="{{ strtolower($r->num_confirmacion . ' ' . ($r->cliente->nombre_cliente ?? '') . ' ' . ($r->cliente->apellido_cliente ?? '') . ' ' . ($r->horario->pelicula->nom_pelicula ?? '')) }}">

                    <td class="ps-4">
                        <code class="text-danger fw-bold">{{ $r->num_confirmacion }}</code>
                        <div style="font-size:.7rem" class="text-secondary">
                            {{ \Carbon\Carbon::parse($r->fecha_compra)->format('d/m/Y') }}
                        </div>
                    </td>

                    <td>
                        <div class="fw-bold">
                            {{ $r->cliente->nombre_cliente ?? 'N/A' }}
                            {{ $r->cliente->apellido_cliente ?? '' }}
                        </div>
                        <small class="text-secondary">{{ $r->cliente->correo_cli ?? '' }}</small>
                    </td>

                    <td>
                        <div class="fw-bold">{{ $r->horario->pelicula->nom_pelicula ?? 'N/A' }}</div>
                        <small class="text-secondary">
                            {{ isset($r->horario->fecha) ? \Carbon\Carbon::parse($r->horario->fecha)->format('d/m/Y') : '' }}
                            {{ isset($r->horario->hora_inicio) ? \Carbon\Carbon::parse($r->horario->hora_inicio)->format('h:i A') : '' }}
                            @if($r->horario)
                                · {{ $r->horario->sala->sucursal->nombre_suc ?? '' }}
                            @endif
                        </small>
                    </td>

                    <td>
                        <div class="d-flex flex-wrap gap-1" style="max-width:130px">
                            @foreach($r->asientos->take(4) as $a)
                                <span class="badge" style="background:#2a2a2a;color:#ccc;font-size:.65rem">
                                    {{ $a->num_fila }}{{ $a->num_asiento }}
                                </span>
                            @endforeach
                            @if($r->asientos->count() > 4)
                                <span class="badge bg-secondary" style="font-size:.65rem">
                                    +{{ $r->asientos->count() - 4 }}
                                </span>
                            @endif
                        </div>
                    </td>

                    <td class="fw-bold">${{ number_format($r->monto, 2) }}</td>

                    <td>
                        <span class="badge bg-secondary">{{ $r->metodo_pago }}</span>
                    </td>

                    <td>
                        <span class="badge {{ $r->estado === 'Confirmada' ? 'bg-success' : 'bg-danger' }}">
                            {{ $r->estado }}
                        </span>
                        @if($r->ordenes->isNotEmpty())
                            <span class="badge bg-secondary ms-1" title="Incluye pedido de dulcería">
                                <i class="bi bi-cup-straw"></i>
                            </span>
                        @endif
                    </td>

                    <td class="text-end pe-4">
                        {{-- Ver detalle --}}
                        <button type="button"
                                class="btn btn-sm btn-outline-info me-1"
                                title="Ver detalle"
                                onclick="verDetalle({{ $r->id_reserva }})">
                            <i class="bi bi-eye"></i>
                        </button>

                        {{-- Cancelar (solo si está confirmada) --}}
                        @if($r->estado === 'Confirmada')
                        <form action="{{ route('admin.reservas.destroy', $r->id_reserva) }}"
                              method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                title="Cancelar reserva"
                                data-cp-confirm="¿Cancelar la reserva {{ $r->num_confirmacion }} de {{ $r->cliente->nombre_cliente ?? 'este cliente' }}? Los asientos quedarán libres y se devolverá el stock de snacks."
                                data-cp-title="Cancelar reserva"
                                data-cp-btn-text="Sí, cancelar">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-secondary py-5">
                        <i class="bi bi-ticket-detailed fs-2 d-block mb-2"></i>
                        No hay reservas registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ Modal de detalle de reserva ═══ --}}
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:#1f1f1f;border:1px solid #444">
            <div class="modal-header" style="border-color:#333">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-ticket-detailed text-danger me-2"></i>
                    Detalle de reserva
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetalleBody">
                <div class="text-center py-4 text-secondary">
                    <span class="spinner-border spinner-border-sm me-2"></span>Cargando...
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pre-procesamos los datos en PHP para evitar errores del parser de Blade
     al tener closures con arrays anidados dentro de @json() --}}
@php
    $reservasJson = $reservas->map(function ($r) {
        return [
            'id'             => $r->id_reserva,
            'codigo'         => $r->num_confirmacion,
            'estado'         => $r->estado,
            'fecha_compra'   => $r->fecha_compra
                                    ? \Carbon\Carbon::parse($r->fecha_compra)->format('d/m/Y')
                                    : '',
            'monto'          => number_format($r->monto, 2),
            'metodo_pago'    => $r->metodo_pago,
            'cliente_nombre' => ($r->cliente->nombre_cliente ?? '') . ' ' . ($r->cliente->apellido_cliente ?? ''),
            'cliente_correo' => $r->cliente->correo_cli ?? '',
            'pelicula'       => $r->horario->pelicula->nom_pelicula ?? '',
            'fecha_funcion'  => $r->horario->fecha
                                    ? \Carbon\Carbon::parse($r->horario->fecha)->format('d/m/Y')
                                    : '',
            'hora_funcion'   => $r->horario->hora_inicio
                                    ? \Carbon\Carbon::parse($r->horario->hora_inicio)->format('h:i A')
                                    : '',
            'tecnologia'     => $r->horario->tec_proyecc ?? '',
            'sala'           => $r->horario->sala->num_sala ?? '',
            'sucursal'       => $r->horario->sala->sucursal->nombre_suc ?? '',
            'asientos'       => $r->asientos->map(fn ($a) => $a->num_fila . $a->num_asiento)->values(),
            'ordenes'        => $r->ordenes->map(fn ($o) => [
                'total'     => number_format($o->total, 2),
                'productos' => $o->productos->map(fn ($p) => [
                    'nombre'   => $p->nom_productos,
                    'cantidad' => $p->pivot->cantidad,
                    'precio'   => number_format($p->precio_producto * $p->pivot->cantidad, 2),
                ])->values(),
            ])->values(),
        ];
    });
@endphp

{{-- Datos de reservas para el modal (JSON embebido) --}}
<script>
const reservasData = @json($reservasJson);

function verDetalle(id) {
    const r = reservasData.find(x => x.id === id);
    if (!r) return;

    const estadoBadge = r.estado === 'Confirmada'
        ? '<span class="badge bg-success">Confirmada</span>'
        : '<span class="badge bg-danger">Cancelada</span>';

    let asientosHtml = r.asientos.length
        ? r.asientos.map(a => `<span class="badge me-1 mb-1" style="background:#2a2a2a;color:#ccc">${a}</span>`).join('')
        : '<span class="text-secondary small">Sin asientos</span>';

    let snacksHtml = '';
    if (r.ordenes.length) {
        r.ordenes.forEach(o => {
            snacksHtml += o.productos.map(p =>
                `<div class="d-flex justify-content-between small py-1" style="border-bottom:1px solid #2a2a2a">
                    <span><span class="badge me-1" style="background:#3a1a1a;color:#e09090">×${p.cantidad}</span>${p.nombre}</span>
                    <span class="text-danger fw-bold">$${p.precio}</span>
                </div>`
            ).join('');
            snacksHtml += `<div class="d-flex justify-content-between small pt-1 fw-bold">
                <span class="text-secondary">Subtotal snacks</span>
                <span>$${o.total}</span>
            </div>`;
        });
    } else {
        snacksHtml = '<p class="text-secondary small mb-0">No incluyó snacks.</p>';
    }

    document.getElementById('modalDetalleBody').innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="text-secondary small mb-1">Código de confirmación</div>
                    <code class="text-danger fw-bold fs-5">${r.codigo}</code>
                    &nbsp;${estadoBadge}
                </div>
                <div class="mb-3">
                    <div class="text-secondary small mb-1"><i class="bi bi-person me-1"></i>Cliente</div>
                    <div class="fw-bold">${r.cliente_nombre.trim() || 'N/A'}</div>
                    <div class="text-secondary small">${r.cliente_correo}</div>
                </div>
                <div class="mb-3">
                    <div class="text-secondary small mb-1"><i class="bi bi-film me-1"></i>Película</div>
                    <div class="fw-bold">${r.pelicula}</div>
                    <div class="text-secondary small">
                        ${r.fecha_funcion} ${r.hora_funcion}
                        ${r.tecnologia ? '· <span class="badge bg-secondary">' + r.tecnologia + '</span>' : ''}
                    </div>
                    <div class="text-secondary small">
                        <i class="bi bi-building me-1"></i>${r.sucursal} — Sala ${r.sala}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="text-secondary small mb-1"><i class="bi bi-grid me-1"></i>Asientos</div>
                    <div>${asientosHtml}</div>
                </div>
                <div class="mb-3">
                    <div class="text-secondary small mb-1"><i class="bi bi-cup-straw me-1"></i>Dulcería</div>
                    <div style="background:#111;border-radius:8px;padding:.75rem">${snacksHtml}</div>
                </div>
                <div class="pt-2" style="border-top:1px solid #333">
                    <div class="d-flex justify-content-between small text-secondary">
                        <span>Fecha de compra</span><span>${r.fecha_compra}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-secondary mt-1">
                        <span>Método de pago</span><span>${r.metodo_pago}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mt-2">
                        <span>Total</span>
                        <span class="text-danger">$${r.monto}</span>
                    </div>
                </div>
            </div>
        </div>`;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetalle')).show();
}

// ── Filtros de tabla ──
function filtrar() {
    const texto  = document.getElementById('filtroTexto').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value;
    const pago   = document.getElementById('filtroPago').value;

    document.querySelectorAll('#tablaReservas tbody tr[data-estado]').forEach(tr => {
        const matchTexto  = !texto  || tr.dataset.texto.includes(texto);
        const matchEstado = !estado || tr.dataset.estado === estado;
        const matchPago   = !pago   || tr.dataset.pago === pago;
        tr.style.display  = (matchTexto && matchEstado && matchPago) ? '' : 'none';
    });
}

function limpiarFiltros() {
    document.getElementById('filtroTexto').value  = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroPago').value   = '';
    filtrar();
}

document.getElementById('filtroTexto').addEventListener('input', filtrar);
document.getElementById('filtroEstado').addEventListener('change', filtrar);
document.getElementById('filtroPago').addEventListener('change', filtrar);
</script>
@endsection
