@extends('layouts.admin')
@section('title', 'Clientes')
@section('content')
<h3 class="fw-bold mb-4">Clientes registrados</h3>
<div class="card-admin p-0 overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th class="ps-4">Cliente</th>
                <th>Correo</th>
                <th>Edad</th>
                <th>Teléfono</th>
                <th>Reservas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clientes as $c)
            <tr>
                <td class="ps-4 fw-bold">{{ $c->nombre_cliente }} {{ $c->apellido_cliente }}</td>
                <td>{{ $c->correo_cli }}</td>
                <td>{{ $c->edad_cli }} años</td>
                <td>{{ $c->contacto_cli }}</td>
                <td><span class="badge bg-secondary">{{ $c->reservas_count }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-secondary py-4">No hay clientes.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
