@extends('layouts.admin')
@section('title', isset($sala) ? 'Editar Sala' : 'Nueva Sala')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.salas.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h3 class="fw-bold mb-0">{{ isset($sala) ? 'Editar Sala ' . $sala->num_sala : 'Nueva Sala' }}</h3>
        <small class="text-secondary">
            {{ isset($sala) ? $sala->sucursal->nombre_suc ?? '' : 'Completa los datos para crear la sala y sus asientos' }}
        </small>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row g-4" style="max-width:900px">

    {{-- ── Formulario principal ── --}}
    <div class="col-md-7">
        <div class="card-admin p-4">
            <form action="{{ isset($sala) ? route('admin.salas.update', $sala->id_sala) : route('admin.salas.store') }}"
                  method="POST">
                @csrf
                @if(isset($sala)) @method('PUT') @endif

                <div class="row g-3">

                    {{-- Sucursal --}}
                    <div class="col-12">
                        <label class="form-label">Sucursal *</label>
                        <select name="id_suc2" class="form-select" required>
                            <option value="">-- Selecciona una sucursal --</option>
                            @foreach($sucursales as $suc)
                            <option value="{{ $suc->id_suc }}"
                                {{ old('id_suc2', $sala->id_suc2 ?? '') == $suc->id_suc ? 'selected' : '' }}>
                                {{ $suc->nombre_suc }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Número de sala --}}
                    <div class="col-md-6">
                        <label class="form-label">Número de Sala *</label>
                        <input type="number" name="num_sala" class="form-control"
                               min="1" max="99" required
                               value="{{ old('num_sala', $sala->num_sala ?? '') }}"
                               placeholder="Ej: 1, 2, 3...">
                        <div class="form-text text-secondary">Debe ser único dentro de la sucursal.</div>
                    </div>

                    {{-- ── Sección de capacidad ── --}}
                    <div class="col-12 mt-2">
                        <div style="border-top:1px solid #333;padding-top:1rem">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="fw-bold">Distribución de asientos</div>
                                    <small class="text-secondary">
                                        @if(isset($sala))
                                            Actualmente: <strong>{{ $sala->capaci_sala }}</strong> asientos
                                            ({{ $filasActuales }} filas × {{ $asPorFilaActual }} por fila)
                                        @else
                                            Define filas y asientos por fila
                                        @endif
                                    </small>
                                </div>
                                @if(isset($sala) && $tieneReservas)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-lock me-1"></i>Bloqueado
                                    </span>
                                @endif
                            </div>

                            @if(isset($sala) && $tieneReservas)
                                {{-- Capacidad bloqueada por reservas activas --}}
                                <div class="alert alert-warning py-2 mb-0" style="font-size:.85rem">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    No se puede modificar la capacidad mientras haya <strong>reservas activas</strong>
                                    en esta sala. Cancela las reservas primero.
                                </div>
                            @else
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Número de filas *
                                            <small class="text-secondary">(A=1, B=2… máx. 26)</small>
                                        </label>
                                        <input type="number" name="filas" id="filas"
                                               class="form-control" min="1" max="26"
                                               value="{{ old('filas', $filasActuales ?? '') }}"
                                               placeholder="Ej: 5" {{ isset($sala) ? '' : 'required' }}
                                               oninput="calcularTotal()">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Asientos por fila *
                                            <small class="text-secondary">(máx. 30)</small>
                                        </label>
                                        <input type="number" name="asientos_por_fila" id="asPorFila"
                                               class="form-control" min="1" max="30"
                                               value="{{ old('asientos_por_fila', $asPorFilaActual ?? '') }}"
                                               placeholder="Ej: 10" {{ isset($sala) ? '' : 'required' }}
                                               oninput="calcularTotal()">
                                    </div>

                                    {{-- Total calculado en tiempo real --}}
                                    <div class="col-12">
                                        <div style="background:#111;border-radius:8px;padding:.75rem 1rem;border:1px solid #333">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-secondary small">Total de asientos a generar:</span>
                                                <span class="fw-bold fs-5 text-danger" id="totalAsientos">—</span>
                                            </div>
                                            <div class="text-secondary small mt-1" id="filasPreview"></div>
                                        </div>
                                    </div>

                                    @if(isset($sala))
                                    <div class="col-12">
                                        <div class="alert alert-info py-2 mb-0" style="font-size:.85rem">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Si cambias la capacidad, <strong>todos los asientos actuales serán eliminados</strong>
                                            y se generarán de nuevo. Solo es posible si no hay reservas activas.
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-cine px-5">
                            <i class="bi bi-save me-2"></i>{{ isset($sala) ? 'Guardar cambios' : 'Crear sala' }}
                        </button>
                        <a href="{{ route('admin.salas.index') }}" class="btn btn-outline-secondary ms-2">
                            Cancelar
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ── Panel informativo (solo en edición) ── --}}
    @if(isset($sala))
    <div class="col-md-5">
        <div class="card-admin p-4">
            <div class="fw-bold mb-3">
                <i class="bi bi-grid-3x3-gap me-2 text-danger"></i>
                Distribución de asientos
            </div>
            @php
                $filasAgrupadas = $sala->asientos->groupBy('num_fila')->sortKeys();
            @endphp
            @forelse($filasAgrupadas as $letra => $asientosFila)
            <div class="mb-2 d-flex align-items-center gap-2">
                <span class="text-secondary fw-bold" style="width:18px;font-size:.8rem;flex-shrink:0">{{ $letra }}</span>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($asientosFila->sortBy('num_asiento') as $a)
                    <span style="width:26px;height:26px;background:#2a2a2a;border:1px solid #444;
                                 border-radius:4px;display:flex;align-items:center;justify-content:center;
                                 font-size:.65rem;color:#aaa">
                        {{ $a->num_asiento }}
                    </span>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-secondary small">Esta sala no tiene asientos generados.</p>
            @endforelse

            @if($filasAgrupadas->isNotEmpty())
            <div class="mt-3 pt-3 d-flex gap-3" style="border-top:1px solid #333">
                <div class="text-secondary small">
                    <span class="fw-bold text-white">{{ $filasAgrupadas->count() }}</span> filas
                </div>
                <div class="text-secondary small">
                    <span class="fw-bold text-white">{{ $filasAgrupadas->first()->count() }}</span> por fila
                </div>
                <div class="text-secondary small">
                    <span class="fw-bold text-white">{{ $sala->capaci_sala }}</span> total
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

<script>
function calcularTotal() {
    const filas = parseInt(document.getElementById('filas')?.value) || 0;
    const porFila = parseInt(document.getElementById('asPorFila')?.value) || 0;
    const total = filas * porFila;
    const letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    document.getElementById('totalAsientos').textContent = total > 0 ? total + ' asientos' : '—';

    if (filas > 0 && porFila > 0) {
        const filasStr = Array.from({length: filas}, (_, i) => `Fila ${letras[i]} (${porFila})`).join(' · ');
        document.getElementById('filasPreview').textContent = filasStr;
    } else {
        document.getElementById('filasPreview').textContent = '';
    }
}
// Calcular al cargar si ya hay valores (modo edición)
calcularTotal();
</script>

@endsection
