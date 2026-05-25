@extends('layouts.admin')
@section('title', 'Salas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Salas</h3>
        <small class="text-secondary">
            {{ session('admin')['sucursal']['nombre_suc'] ?? '' }}
            · {{ $salas->count() }} sala(s) registradas
        </small>
    </div>
    <a href="{{ route('admin.salas.create') }}" class="btn btn-cine">
        <i class="bi bi-plus-lg me-1"></i>Nueva Sala
    </a>
</div>

@if($salas->isEmpty())
<div class="card-admin p-5 text-center text-secondary">
    <i class="bi bi-grid-3x3-gap fs-1 d-block mb-3 opacity-50"></i>
    <h6 class="text-secondary">No hay salas en esta sucursal</h6>
    <p class="small mb-3">Crea la primera sala para empezar a programar funciones.</p>
    <a href="{{ route('admin.salas.create') }}" class="btn btn-cine btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Crear primera sala
    </a>
</div>
@else

<div class="card-admin p-0 overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th class="ps-4">Sala</th>
                <th>Capacidad</th>
                <th>Asientos</th>
                <th>Funciones programadas</th>
                <th class="text-end pe-4">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salas as $s)
            <tr>
                <td class="ps-4">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;background:rgba(229,9,20,.12);border-radius:10px;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <span style="font-size:1.2rem;font-weight:900;color:var(--cp-rojo)">{{ $s->num_sala }}</span>
                        </div>
                        <div class="fw-bold">Sala {{ $s->num_sala }}</div>
                    </div>
                </td>
                <td>
                    <span class="fw-bold">{{ $s->capaci_sala }}</span>
                    <span class="text-secondary small"> asientos</span>
                </td>
                <td>
                    @if($s->asientos_count > 0)
                        <span class="text-secondary small">{{ $s->asientos_count }} generados</span>
                    @else
                        <span class="badge bg-warning text-dark" style="font-size:.7rem">Sin asientos</span>
                    @endif
                </td>
                <td>
                    @if($s->horarios_count > 0)
                        <span class="badge bg-secondary">{{ $s->horarios_count }} función(es)</span>
                    @else
                        <span class="text-secondary small">—</span>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <a href="{{ route('admin.salas.edit', $s->id_sala) }}"
                       class="btn btn-sm btn-outline-warning me-1" title="Editar sala">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.salas.destroy', $s->id_sala) }}"
                          method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            title="Eliminar sala"
                            data-cp-confirm="¿Eliminar la Sala {{ $s->num_sala }}? Se eliminarán también todos sus asientos. Esta acción no se puede deshacer."
                            data-cp-title="Eliminar Sala {{ $s->num_sala }}"
                            data-cp-btn-text="Sí, eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endif

@endsection
