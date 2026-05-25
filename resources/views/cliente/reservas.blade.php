@extends('layouts.app')

@section('title', 'Mis Reservas — CinePlus')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-1">🎟️ Mis Reservas</h2>
    <p class="text-secondary mb-4">
        Hola, <strong>{{ session('cliente')['nombre_cliente'] }}</strong> — aquí están todas tus reservas.
    </p>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($reservas->isEmpty())
    <div class="text-center py-5 text-secondary">
        <i class="bi bi-ticket-detailed fs-1 d-block mb-3"></i>
        <p>Aún no tienes reservas.</p>
        <a href="{{ route('horarios.index') }}" class="btn btn-cine mt-2">
            <i class="bi bi-calendar3 me-2"></i>Ver cartelera
        </a>
    </div>
    @else
    <div class="row g-4">
        @foreach($reservas as $r)
        <div class="col-md-6">
            <div class="card h-100" style="background:var(--cp-gris);border:1px solid #333">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <code class="text-danger fw-bold fs-6">{{ $r->num_confirmacion }}</code>
                            <h5 class="fw-bold mt-1 mb-0">
                                {{ $r->horario->pelicula->nom_pelicula ?? 'Película' }}
                            </h5>
                        </div>
                        <span class="badge {{ $r->estado === 'Confirmada' ? 'bg-success' : ($r->estado === 'Cancelada' ? 'bg-danger' : 'bg-warning text-dark') }} ms-2">
                            {{ $r->estado }}
                        </span>
                    </div>

                    <div class="text-secondary small mb-3">
                        <div class="mb-1">
                            <i class="bi bi-calendar-event text-danger me-2"></i>
                            {{ isset($r->horario->fecha) ? \Carbon\Carbon::parse($r->horario->fecha)->locale('es')->isoFormat('dddd D [de] MMMM YYYY') : 'N/A' }}
                        </div>
                        <div class="mb-1">
                            <i class="bi bi-clock text-danger me-2"></i>
                            {{ isset($r->horario->hora_inicio) ? \Carbon\Carbon::parse($r->horario->hora_inicio)->format('h:i A') : '' }}
                            &nbsp;<span class="badge bg-secondary">{{ $r->horario->tec_proyecc ?? '' }}</span>
                        </div>
                        <div class="mb-1">
                            <i class="bi bi-building text-danger me-2"></i>
                            {{ $r->horario->sala->sucursal->nombre_suc ?? 'N/A' }}
                            — Sala {{ $r->horario->sala->num_sala ?? '' }}
                        </div>
                        <div>
                            <i class="bi bi-credit-card text-danger me-2"></i>
                            {{ $r->metodo_pago }} &mdash; <strong>${{ number_format($r->monto, 2) }}</strong>
                        </div>
                    </div>

                    @if($r->asientos->isNotEmpty())
                    <div class="mb-2">
                        <small class="text-secondary d-block mb-1">
                            <i class="bi bi-grid me-1"></i>Asientos:
                        </small>
                        @foreach($r->asientos as $a)
                            <span class="badge me-1" style="background:#2a2a2a;color:#fff">
                                {{ $a->num_fila }}{{ $a->num_asiento }}
                            </span>
                        @endforeach
                    </div>
                    @endif

                    {{-- ── Snacks / Dulcería ── --}}
                    @if($r->ordenes->isNotEmpty())
                    <div class="mb-2 pt-2" style="border-top:1px solid #333">
                        <small class="text-secondary d-block mb-2">
                            <i class="bi bi-cup-straw text-danger me-1"></i>Dulcería:
                        </small>
                        <div class="d-flex flex-column gap-1">
                            @foreach($r->ordenes as $orden)
                                @foreach($orden->productos as $prod)
                                <div class="d-flex justify-content-between align-items-center small">
                                    <span style="color:#ccc">
                                        <span class="badge me-1" style="background:#3a1a1a;color:#e09090">
                                            × {{ $prod->pivot->cantidad }}
                                        </span>
                                        {{ $prod->nom_productos }}
                                    </span>
                                    <span class="text-danger fw-bold">
                                        ${{ number_format($prod->precio_producto * $prod->pivot->cantidad, 2) }}
                                    </span>
                                </div>
                                @endforeach
                                @if($orden->productos->count() > 1)
                                <div class="d-flex justify-content-between small text-secondary pt-1" style="border-top:1px dashed #444">
                                    <span>Subtotal snacks</span>
                                    <span>${{ number_format($orden->total, 2) }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="text-secondary" style="font-size:.75rem">
                        Comprado el {{ \Carbon\Carbon::parse($r->fecha_compra)->format('d/m/Y') }}
                    </div>

                    {{-- ── Acciones (solo si está confirmada) ── --}}
                    @if($r->estado === 'Confirmada')
                    <div class="d-flex gap-2 mt-3 pt-3" style="border-top:1px solid #333">
                        <a href="{{ route('reservas.edit', $r->id_reserva) }}"
                           class="btn btn-sm btn-outline-warning flex-fill">
                            <i class="bi bi-pencil-square me-1"></i>Modificar
                        </a>
                        <form action="{{ route('reservas.cancelar', $r->id_reserva) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                data-cp-confirm="¿Cancelar la reserva {{ $r->num_confirmacion }}? Los asientos quedarán libres y no podrás deshacer esta acción."
                                data-cp-title="Cancelar reserva"
                                data-cp-btn-text="Sí, cancelar reserva">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
