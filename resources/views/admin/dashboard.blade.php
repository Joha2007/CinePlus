@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- Encabezado --}}
<div class="mb-5">
    <div class="d-flex align-items-center gap-3 mb-1">
        <h3 class="fw-bold mb-0">Bienvenido, {{ session('admin')['nombre_adm'] }} 👋</h3>
    </div>
    <p class="text-secondary mb-0" style="font-size:.9rem">
        Panel de control · {{ session('admin')['sucursal']['nombre_suc'] ?? '' }}
        · {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd D [de] MMMM YYYY') }}
    </p>
</div>

{{-- ══ Estadísticas rápidas ══ --}}
<div class="row g-3 mb-5">
    <div class="col-sm-6 col-xl-3">
        <div class="card-admin p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="width:46px;height:46px;background:rgba(229,9,20,.15);border-radius:12px;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-film fs-5" style="color:var(--cp-rojo)"></i>
                </div>
                <span class="badge bg-secondary" style="font-size:.68rem">Global</span>
            </div>
            <div class="fw-bold" style="font-size:2rem;line-height:1">{{ $stats['peliculas'] }}</div>
            <div class="text-secondary small mt-1">Películas en cartelera</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-admin p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="width:46px;height:46px;background:rgba(34,197,94,.12);border-radius:12px;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-ticket-detailed fs-5" style="color:#22c55e"></i>
                </div>
                <span class="badge" style="background:rgba(34,197,94,.15);color:#86efac;font-size:.68rem">Mi sucursal</span>
            </div>
            <div class="fw-bold" style="font-size:2rem;line-height:1">{{ $stats['reservas'] }}</div>
            <div class="text-secondary small mt-1">Reservas totales</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-admin p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="width:46px;height:46px;background:rgba(6,182,212,.12);border-radius:12px;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-people fs-5" style="color:#06b6d4"></i>
                </div>
                <span class="badge bg-secondary" style="font-size:.68rem">Global</span>
            </div>
            <div class="fw-bold" style="font-size:2rem;line-height:1">{{ $stats['clientes'] }}</div>
            <div class="text-secondary small mt-1">Clientes registrados</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-admin p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="width:46px;height:46px;background:rgba(234,179,8,.12);border-radius:12px;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-calendar3 fs-5" style="color:#eab308"></i>
                </div>
                <span class="badge" style="background:rgba(234,179,8,.12);color:#fde68a;font-size:.68rem">Mi sucursal</span>
            </div>
            <div class="fw-bold" style="font-size:2rem;line-height:1">{{ $stats['horarios'] }}</div>
            <div class="text-secondary small mt-1">Funciones próximas</div>
        </div>
    </div>
</div>

{{-- ══ Sección inferior ══ --}}
<div class="row g-4">

    {{-- Últimas reservas --}}
    <div class="col-lg-7">
        <div class="card-admin">
            <div class="d-flex justify-content-between align-items-center p-4 pb-0">
                <div>
                    <h6 class="fw-bold mb-0">Últimas Reservas</h6>
                    <small class="text-secondary">{{ session('admin')['sucursal']['nombre_suc'] ?? '' }}</small>
                </div>
                <a href="{{ route('admin.reservas.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem">
                    Ver todas <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="p-4 pt-3">
                @if($ultimasReservas->isEmpty())
                <div class="text-center text-secondary py-4">
                    <i class="bi bi-ticket-detailed fs-2 d-block mb-2 opacity-50"></i>
                    <small>No hay reservas aún.</small>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Película</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasReservas as $reserva)
                            <tr>
                                <td>
                                    <code style="color:var(--cp-rojo);font-size:.75rem">
                                        {{ $reserva->num_confirmacion }}
                                    </code>
                                </td>
                                <td class="small">{{ $reserva->cliente->nombre_cliente ?? '—' }}</td>
                                <td class="small" style="max-width:140px">
                                    <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                        {{ $reserva->horario->pelicula->nom_pelicula ?? '—' }}
                                    </span>
                                </td>
                                <td class="small fw-bold">${{ number_format($reserva->monto, 2) }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($reserva->estado) {
                                            'Confirmada' => 'bg-success',
                                            'Cancelada'  => 'bg-danger',
                                            default      => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="font-size:.68rem">
                                        {{ $reserva->estado }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Próximas funciones --}}
    <div class="col-lg-5">
        <div class="card-admin">
            <div class="d-flex justify-content-between align-items-center p-4 pb-0">
                <div>
                    <h6 class="fw-bold mb-0">Próximas Funciones</h6>
                    <small class="text-secondary">{{ session('admin')['sucursal']['nombre_suc'] ?? '' }}</small>
                </div>
                <a href="{{ route('admin.horarios.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem">
                    Ver todas <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="p-4 pt-3">
                @if($proximasFunc->isEmpty())
                <div class="text-center text-secondary py-4">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                    <small>No hay funciones próximas.</small>
                </div>
                @else
                @foreach($proximasFunc as $h)
                <div class="d-flex align-items-center gap-3 py-3" style="border-bottom:1px solid var(--cp-borde)">
                    {{-- Fecha --}}
                    <div class="text-center flex-shrink-0"
                         style="width:46px;background:rgba(229,9,20,.1);border-radius:8px;padding:.4rem .3rem">
                        <div style="font-size:.65rem;font-weight:700;color:var(--cp-rojo);text-transform:uppercase">
                            {{ \Carbon\Carbon::parse($h->fecha)->locale('es')->isoFormat('MMM') }}
                        </div>
                        <div style="font-size:1.3rem;font-weight:900;line-height:1;color:#fff">
                            {{ \Carbon\Carbon::parse($h->fecha)->format('d') }}
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-bold small text-truncate">{{ $h->pelicula->nom_pelicula }}</div>
                        <div class="text-secondary d-flex align-items-center gap-2 mt-1" style="font-size:.73rem">
                            <span>
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A') }}
                            </span>
                            <span>·</span>
                            <span>Sala {{ $h->sala->num_sala }}</span>
                            <span class="badge bg-secondary ms-1">{{ $h->tec_proyecc }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

</div>

@endsection
