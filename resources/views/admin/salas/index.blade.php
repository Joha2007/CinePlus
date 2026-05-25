@extends('layouts.admin')
@section('title', 'Salas')
@section('content')
<h3 class="fw-bold mb-4">Salas</h3>
<div class="card-admin p-0 overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th class="ps-4">#</th>
                <th>Sala</th>
                <th>Sucursal</th>
                <th>Capacidad</th>
                <th>Asientos disponibles</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salas as $s)
            <tr>
                <td class="ps-4">{{ $s->id_sala }}</td>
                <td class="fw-bold">Sala {{ $s->num_sala }}</td>
                <td>{{ $s->sucursal->nombre_suc ?? 'N/A' }}</td>
                <td>{{ $s->capaci_sala }}</td>
                <td>
                    {{ $s->asientos->where('estado','Disponible')->count() }}
                    / {{ $s->asientos->count() }}
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-secondary py-4">No hay salas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
