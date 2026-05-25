@extends('layouts.app')

@section('title', 'Modificar reserva — ' . ($reserva->horario->pelicula->nom_pelicula ?? 'Función') . ' — CinePlus')

@section('content')
<div class="container py-5" style="max-width:900px">

    {{-- Cabecera de la función --}}
    <div class="mb-4 d-flex align-items-start gap-4">
        <img src="{{ $reserva->horario->pelicula->img ? asset('storage/'.$reserva->horario->pelicula->img) : asset('images/no-poster.svg') }}"
             alt="Póster"
             style="width:90px;border-radius:8px;object-fit:cover"
             onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
        <div>
            <div class="mb-1">
                <span class="badge bg-warning text-dark me-2">
                    <i class="bi bi-pencil me-1"></i>Modificando reserva
                </span>
                <code class="text-danger fw-bold">{{ $reserva->num_confirmacion }}</code>
            </div>
            <h2 class="fw-bold mb-1">{{ $reserva->horario->pelicula->nom_pelicula ?? 'Función' }}</h2>
            <p class="text-secondary mb-0">
                <i class="bi bi-calendar-event text-danger me-2"></i>
                {{ \Carbon\Carbon::parse($reserva->horario->fecha)->locale('es')->isoFormat('dddd D [de] MMMM YYYY') }}
                &nbsp;·&nbsp;
                <i class="bi bi-clock text-danger me-2"></i>
                {{ \Carbon\Carbon::parse($reserva->horario->hora_inicio)->format('h:i A') }}
                &nbsp;
                <span class="badge bg-secondary">{{ $reserva->horario->tec_proyecc }}</span>
            </p>
            <p class="text-secondary mb-0 small">
                <i class="bi bi-building text-danger me-2"></i>
                {{ $reserva->horario->sala->sucursal->nombre_suc ?? '' }}
                — Sala {{ $reserva->horario->sala->num_sala ?? '' }}
            </p>
        </div>
    </div>

    <form action="{{ route('reservas.update', $reserva->id_reserva) }}" method="POST" id="formEditar">
        @csrf
        @method('PUT')

        @error('asientos')
            <div class="alert alert-danger py-2">{{ $message }}</div>
        @enderror

        {{-- Mapa de asientos --}}
        <div class="mb-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-grid me-2 text-danger"></i>Selecciona tus asientos
            </h5>

            {{-- Leyenda --}}
            <div class="d-flex gap-3 mb-4 flex-wrap">
                <span class="d-flex align-items-center gap-2 small text-secondary">
                    <span style="width:26px;height:26px;background:#2a2a2a;border:1px solid #555;border-radius:4px;display:inline-block"></span>
                    Disponible
                </span>
                <span class="d-flex align-items-center gap-2 small text-secondary">
                    <span style="width:26px;height:26px;background:var(--cp-rojo);border-radius:4px;display:inline-block"></span>
                    Seleccionado
                </span>
                <span class="d-flex align-items-center gap-2 small text-secondary">
                    <span style="width:26px;height:26px;background:#444;border:1px solid #666;border-radius:4px;opacity:.5;display:inline-block"></span>
                    Ocupado
                </span>
            </div>

            {{-- Pantalla --}}
            <div class="text-center mb-4">
                <div style="
                    background:linear-gradient(to bottom,#e0e0e0,#aaa);
                    height:8px;
                    border-radius:4px 4px 0 0;
                    max-width:400px;
                    margin:0 auto 6px;
                "></div>
                <small class="text-secondary">PANTALLA</small>
            </div>

            {{-- Filas --}}
            <div class="d-flex flex-column align-items-center gap-2">
                @foreach($asientos as $fila => $asientosFila)
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary fw-bold" style="width:20px;text-align:right;font-size:.8rem">{{ $fila }}</span>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($asientosFila->sortBy('num_asiento') as $a)
                            @php $esMio = $asientosActuales->contains($a->id_asiento) @endphp
                            @if(!in_array($a->id_asiento, $ocupadosIds) || $esMio)
                                <button type="button"
                                    class="btn-asiento {{ $esMio ? 'seleccionado' : 'disponible' }}"
                                    data-id="{{ $a->id_asiento }}"
                                    data-label="{{ $a->num_fila }}{{ $a->num_asiento }}"
                                    title="{{ $a->num_fila }}{{ $a->num_asiento }}{{ $esMio ? ' — Tu asiento actual' : '' }}">
                                    {{ $a->num_asiento }}
                                </button>
                            @else
                                <button type="button" class="btn-asiento ocupado" disabled
                                    title="{{ $a->num_fila }}{{ $a->num_asiento }} — Ocupado">
                                    {{ $a->num_asiento }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Hidden inputs para asientos seleccionados --}}
            <div id="asientosHidden"></div>
        </div>

        {{-- ══════════ SECCIÓN SNACKS ══════════ --}}
        <div class="mb-4">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-cup-straw text-danger me-2"></i>Dulcería <span class="badge bg-secondary fw-normal" style="font-size:.7rem">Opcional</span>
            </h5>
            <p class="text-secondary small mb-3">Ajusta los snacks de tu pedido.</p>

            @error('snacks')
                <div class="alert alert-danger py-2 mb-3">{{ $message }}</div>
            @enderror

            @if($productos->isEmpty())
                <p class="text-secondary small">No hay productos disponibles en este momento.</p>
            @else
            <div class="row g-3">
                @foreach($productos as $prod)
                @php
                    $cantActual  = $snacksActuales->get($prod->id_producto, 0);
                    $stockEfectivo = $prod->stock + $cantActual; // stock disponible + lo que ya tenía
                    $maxCantidad = min($stockEfectivo, 10);
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card-snack {{ $cantActual > 0 ? 'activo' : '' }}" data-precio="{{ $prod->precio_producto }}">
                        <img src="{{ $prod->img ? asset('storage/'.$prod->img) : asset('images/no-poster.svg') }}"
                             alt="{{ $prod->nom_productos }}"
                             onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
                        <div class="card-snack-body">
                            <div class="fw-bold small mb-0 text-truncate">{{ $prod->nom_productos }}</div>
                            <div class="text-danger fw-bold">${{ number_format($prod->precio_producto, 2) }}</div>
                            <div class="text-secondary" style="font-size:.68rem">Disp: {{ $stockEfectivo }}</div>
                            {{-- Selector cantidad --}}
                            <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                                <button type="button"
                                        class="btn-qty btn-qty-minus"
                                        data-id="{{ $prod->id_producto }}">−</button>
                                <input type="number"
                                       name="snacks[{{ $prod->id_producto }}]"
                                       id="snack-{{ $prod->id_producto }}"
                                       class="snack-qty"
                                       value="{{ $cantActual }}" min="0" max="{{ $maxCantidad }}"
                                       readonly>
                                <button type="button"
                                        class="btn-qty btn-qty-plus"
                                        data-id="{{ $prod->id_producto }}"
                                        data-max="{{ $maxCantidad }}">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ══════════ RESUMEN TOTAL + PAGO ══════════ --}}
        <div class="card p-4 mb-4" style="background:var(--cp-gris);border:1px solid #333">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="mb-1 text-secondary small">Asientos seleccionados:</p>
                    <div id="resumenAsientos" class="d-flex flex-wrap gap-1 mb-2">
                        <span class="text-secondary small">Ninguno aún</span>
                    </div>
                    <div id="resumenSnacks" class="mb-2" style="display:none">
                        <p class="mb-1 text-secondary small">Snacks:</p>
                        <div id="resumenSnacksList" class="d-flex flex-wrap gap-1"></div>
                    </div>
                    <div class="mt-2 pt-2" style="border-top:1px solid #333">
                        <div class="d-flex justify-content-between small text-secondary">
                            <span>Asientos:</span>
                            <span id="subtotalAsientos">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small text-secondary" id="filaSnacks" style="display:none!important">
                            <span>Snacks:</span>
                            <span id="subtotalSnacks">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mt-1">
                            <span>Total:</span>
                            <span class="text-danger" id="totalMonto">$0.00</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-secondary small mb-1">Método de pago</label>
                    <select name="metodo_pago" class="form-select bg-dark text-white border-secondary" required>
                        <option value="">— Selecciona —</option>
                        <option value="Tarjeta"       {{ $reserva->metodo_pago === 'Tarjeta'       ? 'selected' : '' }}>💳 Tarjeta</option>
                        <option value="Efectivo"      {{ $reserva->metodo_pago === 'Efectivo'      ? 'selected' : '' }}>💵 Efectivo</option>
                        <option value="Transferencia" {{ $reserva->metodo_pago === 'Transferencia' ? 'selected' : '' }}>🏦 Transferencia</option>
                    </select>
                    @error('metodo_pago')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-cine px-4" id="btnGuardar" disabled>
                <i class="bi bi-check-circle me-2"></i>Guardar cambios
            </button>
            <a href="{{ route('cliente.reservas') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Volver sin guardar
            </a>
        </div>
    </form>
</div>

<style>
/* ─── Asientos ─── */
.btn-asiento {
    width: 36px; height: 36px;
    border-radius: 5px; border: none;
    font-size: .75rem; font-weight: 600;
    cursor: pointer; transition: transform .1s, background .15s;
}
.btn-asiento.disponible  { background:#2a2a2a; color:#ccc; border:1px solid #555; }
.btn-asiento.disponible:hover { background:#444; transform:scale(1.1); }
.btn-asiento.seleccionado{ background:var(--cp-rojo); color:#fff; border:1px solid transparent; transform:scale(1.05); }
.btn-asiento.ocupado     { background:#333; color:#666; border:1px solid #444; cursor:not-allowed; opacity:.5; }

/* ─── Snack cards ─── */
.card-snack {
    background: #2a2a2a;
    border: 1px solid #444;
    border-radius: 10px;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
}
.card-snack.activo {
    border-color: var(--cp-rojo);
    box-shadow: 0 0 12px rgba(229,9,20,.25);
}
.card-snack img {
    width: 100%; height: 110px; object-fit: cover;
}
.card-snack-body { padding: .6rem .75rem .75rem; text-align: center; }

/* ─── Botones cantidad ─── */
.btn-qty {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: 1px solid #555;
    background: #1f1f1f;
    color: #ccc;
    font-size: 1rem; line-height: 1;
    cursor: pointer; transition: background .15s;
    display:flex; align-items:center; justify-content:center;
}
.btn-qty:hover  { background:#333; }
.btn-qty-plus:hover { background: rgba(229,9,20,.3); border-color: var(--cp-rojo); color:#fff; }
.snack-qty {
    width: 42px; height: 28px;
    background: #1f1f1f; border: 1px solid #555;
    color: #fff; border-radius: 4px;
    text-align: center; font-weight: 700;
    font-size: .85rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ── Asientos ────────────────────────────────────────────
    const seleccionados   = new Map();
    const hiddenContainer = document.getElementById('asientosHidden');
    const resumenEl       = document.getElementById('resumenAsientos');
    const PRECIO_ASIENTO  = 5.00;

    // Pre-cargar asientos que ya tenía esta reserva
    document.querySelectorAll('.btn-asiento.seleccionado').forEach(btn => {
        seleccionados.set(btn.dataset.id, btn.dataset.label);
    });

    document.querySelectorAll('.btn-asiento:not(.ocupado)').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id, label = btn.dataset.label;
            if (seleccionados.has(id)) {
                seleccionados.delete(id);
                btn.classList.replace('seleccionado', 'disponible');
            } else {
                seleccionados.set(id, label);
                btn.classList.replace('disponible', 'seleccionado');
            }
            actualizarUI();
        });
    });

    // ── Snacks ──────────────────────────────────────────────
    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', () => cambiarCantidad(btn.dataset.id, -1));
    });
    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', () => cambiarCantidad(btn.dataset.id, 1));
    });

    function cambiarCantidad(id, delta) {
        const input = document.getElementById('snack-' + id);
        const max   = parseInt(input.max) || 10;
        let   val   = parseInt(input.value) + delta;
        val = Math.max(0, Math.min(val, max));
        input.value = val;
        input.closest('.card-snack').classList.toggle('activo', val > 0);
        actualizarUI();
    }

    // ── Recalcular totales ───────────────────────────────────
    function actualizarUI() {
        // Hidden inputs asientos
        hiddenContainer.innerHTML = '';
        seleccionados.forEach((label, id) => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'asientos[]'; inp.value = id;
            hiddenContainer.appendChild(inp);
        });

        // Resumen asientos
        resumenEl.innerHTML = seleccionados.size === 0
            ? '<span class="text-secondary small">Ninguno aún</span>'
            : [...seleccionados.values()].map(l =>
                `<span class="badge me-1" style="background:#2a2a2a;color:#fff">${l}</span>`
              ).join('');

        // Resumen snacks
        let montoSnacks = 0;
        const snackBadges = [];
        document.querySelectorAll('.snack-qty').forEach(inp => {
            const qty    = parseInt(inp.value);
            const card   = inp.closest('.card-snack');
            const precio = parseFloat(card.dataset.precio);
            const nombre = card.querySelector('.fw-bold.small').textContent.trim();
            if (qty > 0) {
                montoSnacks += precio * qty;
                snackBadges.push(`<span class="badge me-1" style="background:#2a2a2a;color:#fff">${qty}× ${nombre}</span>`);
            }
        });

        const resumenSnacks = document.getElementById('resumenSnacks');
        const listEl        = document.getElementById('resumenSnacksList');
        const filaSnacks    = document.getElementById('filaSnacks');
        const subSnacks     = document.getElementById('subtotalSnacks');

        if (snackBadges.length > 0) {
            listEl.innerHTML = snackBadges.join('');
            resumenSnacks.style.display = '';
            filaSnacks.style.removeProperty('display');
            subSnacks.textContent = '$' + montoSnacks.toFixed(2);
        } else {
            resumenSnacks.style.display = 'none';
            filaSnacks.style.setProperty('display', 'none', 'important');
        }

        // Totales
        const montoAsientos = seleccionados.size * PRECIO_ASIENTO;
        document.getElementById('subtotalAsientos').textContent = '$' + montoAsientos.toFixed(2);
        document.getElementById('totalMonto').textContent       = '$' + (montoAsientos + montoSnacks).toFixed(2);

        // Botón guardar
        document.getElementById('btnGuardar').disabled = seleccionados.size === 0;
    }

    // Calcular totales iniciales con los datos precargados
    actualizarUI();
});
</script>
@endsection
