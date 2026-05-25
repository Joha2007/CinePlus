@extends('layouts.admin')
@section('title', 'Horarios')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Horarios</h3>
        <small class="text-secondary">Funciones programadas</small>
    </div>
    <a href="{{ route('admin.horarios.create') }}" class="btn btn-cine">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Horario
    </a>
</div>
<div class="card-admin p-0 overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th class="ps-4">Película</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Sala / Sucursal</th>
                <th>Tecnología</th>
                <th class="text-end pe-4">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($horarios as $h)
            <tr>
                <td class="ps-4 fw-bold">{{ $h->pelicula->nom_pelicula }}</td>
                <td>{{ \Carbon\Carbon::parse($h->fecha)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A') }}</td>
                <td>
                    <div>Sala {{ $h->sala->num_sala }}</div>
                    <small class="text-secondary">{{ $h->sala->sucursal->nombre_suc ?? '' }}</small>
                </td>
                <td><span class="badge bg-secondary">{{ $h->tec_proyecc }}</span></td>
                <td class="text-end pe-4">
                    <a href="{{ route('admin.horarios.edit', $h->id_horario) }}"
                       class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.horarios.destroy', $h->id_horario) }}"
                          method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            data-cp-confirm="¿Eliminar el horario del {{ \Carbon\Carbon::parse($h->fecha)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A') }} ({{ $h->pelicula->nom_pelicula }})? Las reservas de este horario también se verán afectadas."
                            data-cp-title="Eliminar horario"
                            data-cp-btn-text="Sí, eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-secondary py-5">No hay horarios.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
