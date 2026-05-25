@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h3 class="fw-bold mb-1">Dashboard</h3>
<p class="text-secondary mb-4">Bienvenido, {{ session('admin')['nombre_adm'] }}</p>

{{-- Estadísticas --}}
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="stat-card" style="background:rgba(229,9,20,.15);border:1px solid rgba(229,9,20,.3)">
            <i class="bi bi-film fs-2 text-danger"></i>
            <h2 class="fw-bold mt-2 mb-0">{{ $stats['peliculas'] }}</h2>
            <p class="text-secondary mb-0">Películas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:rgba(32,201,151,.1);border:1px solid rgba(32,201,151,.3)">
            <i class="bi bi-ticket-detailed fs-2" style="color:#20c997"></i>
            <h2 class="fw-bold mt-2 mb-0">{{ $stats['reservas'] }}</h2>
            <p class="text-secondary mb-0">Reservas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:rgba(13,202,240,.1);border:1px solid rgba(13,202,240,.3)">
            <i class="bi bi-people fs-2" style="color:#0dcaf0"></i>
            <h2 class="fw-bold mt-2 mb-0">{{ $stats['clientes'] }}</h2>
            <p class="text-secondary mb-0">Clientes</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:rgba(255,193,7,.1);border:1px solid rgba(255,193,7,.3)">
            <i class="bi bi-calendar3 fs-2 text-warning"></i>
            <h2 class="fw-bold mt-2 mb-0">{{ $stats['horarios'] }}</h2>
            <p class="text-secondary mb-0">Funciones</p>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Últimas reservas --}}
    <div class="col-lg-7">
        <div class="card-admin p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Últimas Reservas</h5>
                <a href="{{ route('admin.reservas.index') }}" class="text-danger text-decoration-none small">Ver todas</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Confirmación</th>
                            <th>Cliente</th>
                            <th>Película</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ultimasReservas as $reserva)
                        <tr>
                            <td><code class="text-danger">{{ $reserva->num_confirmacion }}</code></td>
                            <td>{{ $reserva->cliente->nombre_cliente ?? 'N/A' }}</td>
                            <td>{{ $reserva->horario->pelicula->nom_pelicula ?? 'N/A' }}</td>
                            <td>${{ number_format($reserva->monto, 2) }}</td>
                            <td>
                                <span class="badge {{ $reserva->estado === 'Confirmada' ? 'bg-success' : ($reserva->estado === 'Cancelada' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ $reserva->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Próximas funciones --}}
    <div class="col-lg-5">
        <div class="card-admin p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Próximas Funciones</h5>
                <a href="{{ route('admin.horarios.index') }}" class="text-danger text-decoration-none small">Ver todas</a>
            </div>
            @foreach($proximasFunc as $h)
            <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom:1px solid #333">
                <div class="text-center" style="min-width:45px">
                    <div class="fw-bold text-danger" style="font-size:.8rem">
                        {{ \Carbon\Carbon::parse($h->fecha)->format('M') }}
                    </div>
                    <div class="fw-bold fs-5">{{ \Carbon\Carbon::parse($h->fecha)->format('d') }}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">{{ $h->pelicula->nom_pelicula }}</div>
                    <div class="text-secondary" style="font-size:.75rem">
                        {{ \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A') }}
                        — Sala {{ $h->sala->num_sala }}
                        <span class="badge bg-secondary ms-1">{{ $h->tec_proyecc }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
