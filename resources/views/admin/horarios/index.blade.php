@extends('layouts.admin')
@section('title', 'Horarios')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Horarios</h3>
        <small class="text-secondary">
            {{ session('admin')['sucursal']['nombre_suc'] ?? '' }}
            · {{ $horarios->count() }} función(es) programadas
        </small>
    </div>
    <a href="{{ route('admin.horarios.create') }}" class="btn btn-cine">
        <i class="bi bi-plus-lg me-1"></i>Nueva Función
    </a>
</div>

{{-- Filtro rápido --}}
<div class="card-admin p-3 mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search text-secondary"></i></span>
                <input type="text" id="filtroHorario" class="form-control"
                       placeholder="Buscar película, sala, tecnología...">
            </div>
        </div>
        <div class="col-md-3">
            <select id="filtroTec" class="form-select form-select-sm">
                <option value="">Todas las tecnologías</option>
                <option value="2D">2D</option>
                <option value="3D">3D</option>
                <option value="IMAX">IMAX</option>
            </select>
        </div>
        <div class="col-md-4">
            <select id="filtroSala" class="form-select form-select-sm">
                <option value="">Todas las salas</option>
                @foreach($horarios->pluck('sala')->unique('id_sala')->sortBy('num_sala') as $sala)
                    <option value="Sala {{ $sala->num_sala }}">Sala {{ $sala->num_sala }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="card-admin p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaHorarios">
            <thead>
                <tr>
                    <th class="ps-4">Película</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Sala</th>
                    <th>Tecnología</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($horarios as $h)
                @php
                    $hoy   = \Carbon\Carbon::today();
                    $fecha = \Carbon\Carbon::parse($h->fecha);
                    $pasada = $fecha->lt($hoy);
                @endphp
                <tr data-texto="{{ strtolower($h->pelicula->nom_pelicula . ' Sala ' . $h->sala->num_sala . ' ' . $h->tec_proyecc) }}"
                    data-tec="{{ $h->tec_proyecc }}"
                    data-sala="Sala {{ $h->sala->num_sala }}"
                    style="{{ $pasada ? 'opacity:.55' : '' }}">
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $h->pelicula->img ? asset('storage/'.$h->pelicula->img) : asset('images/no-poster.svg') }}"
                                 width="36" height="50" class="rounded"
                                 style="object-fit:cover;flex-shrink:0"
                                 onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
                            <div>
                                <div class="fw-bold small">{{ $h->pelicula->nom_pelicula }}</div>
                                @if($pasada)
                                    <span class="badge bg-secondary" style="font-size:.62rem">Pasada</span>
                                @else
                                    <span class="badge bg-success" style="font-size:.62rem">Próxima</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-bold">{{ $fecha->format('d/m/Y') }}</div>
                        <div class="text-secondary" style="font-size:.73rem">
                            {{ $fecha->locale('es')->isoFormat('ddd D [de] MMM') }}
                        </div>
                    </td>
                    <td class="small fw-bold">
                        {{ \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A') }}
                    </td>
                    <td>
                        <span class="fw-bold small">Sala {{ $h->sala->num_sala }}</span>
                    </td>
                    <td>
                        <span class="badge
                            @if($h->tec_proyecc === 'IMAX') bg-primary
                            @elseif($h->tec_proyecc === '3D') bg-info text-dark
                            @else bg-secondary
                            @endif">
                            {{ $h->tec_proyecc }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.horarios.edit', $h->id_horario) }}"
                           class="btn btn-sm btn-outline-warning me-1"
                           title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.horarios.destroy', $h->id_horario) }}"
                              method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                data-cp-confirm="¿Eliminar la función &quot;{{ $h->pelicula->nom_pelicula }}&quot; del {{ $fecha->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A') }}? Las reservas de este horario también se verán afectadas."
                                data-cp-title="Eliminar función"
                                data-cp-btn-text="Sí, eliminar"
                                title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-5">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                        No hay horarios programados en esta sucursal.
                        <div class="mt-2">
                            <a href="{{ route('admin.horarios.create') }}" class="btn btn-cine btn-sm">
                                <i class="bi bi-plus-lg me-1"></i>Programar primera función
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function filtrarHorarios() {
    const texto = document.getElementById('filtroHorario').value.toLowerCase();
    const tec   = document.getElementById('filtroTec').value;
    const sala  = document.getElementById('filtroSala').value;

    document.querySelectorAll('#tablaHorarios tbody tr[data-texto]').forEach(tr => {
        const ok = (!texto || tr.dataset.texto.includes(texto))
                && (!tec   || tr.dataset.tec === tec)
                && (!sala  || tr.dataset.sala === sala);
        tr.style.display = ok ? '' : 'none';
    });
}
document.getElementById('filtroHorario').addEventListener('input', filtrarHorarios);
document.getElementById('filtroTec').addEventListener('change', filtrarHorarios);
document.getElementById('filtroSala').addEventListener('change', filtrarHorarios);
</script>
@endpush

@endsection
