@extends('layouts.app')

@section('title', 'Cartelera — CinePlus')

@section('content')
<div class="container py-5">

    <h2 class="fw-bold mb-1">📅 Cartelera</h2>
    <p class="text-secondary mb-4">Próximas funciones disponibles</p>

    @forelse($horarios as $horario)
    <div class="card mb-3" style="background:var(--cp-gris);border:1px solid #333">
        <div class="card-body">
            <div class="row align-items-center g-3">
                {{-- Imagen --}}
                <div class="col-auto">
                    <img src="{{ $horario->pelicula->img ? asset('storage/'.$horario->pelicula->img) : asset('images/no-poster.svg') }}"
                         width="80" height="110" class="rounded object-fit-cover"
                         alt="{{ $horario->pelicula->nom_pelicula }}"
                         onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
                </div>
                {{-- Info --}}
                <div class="col">
                    <h5 class="fw-bold mb-1">{{ $horario->pelicula->nom_pelicula }}</h5>
                    <div class="d-flex flex-wrap gap-3 text-secondary small">
                        <span><i class="bi bi-calendar-event me-1 text-danger"></i>
                            {{ \Carbon\Carbon::parse($horario->fecha)->locale('es')->isoFormat('dddd D [de] MMMM') }}
                        </span>
                        <span><i class="bi bi-clock me-1 text-danger"></i>
                            {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }}
                        </span>
                        <span><i class="bi bi-building me-1 text-danger"></i>
                            {{ $horario->sala->sucursal->nombre_suc ?? 'N/A' }} — Sala {{ $horario->sala->num_sala ?? '' }}
                        </span>
                        <span>
                            <span class="badge bg-secondary">{{ $horario->tec_proyecc }}</span>
                        </span>
                        <span><span class="badge bg-danger">{{ $horario->pelicula->rango_edad }}</span></span>
                    </div>
                </div>
                {{-- Acción --}}
                <div class="col-auto">
                    @if(session('cliente'))
                        <a href="{{ route('reservas.create', ['horario' => $horario->id_horario]) }}"
                           class="btn btn-cine">
                            <i class="bi bi-ticket-detailed me-1"></i>Reservar
                        </a>
                    @else
                        <a href="{{ route('peliculas.show', $horario->pelicula->id_pelicula) }}"
                           class="btn btn-outline-danger">
                            Ver película
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center text-secondary py-5">
        <i class="bi bi-calendar-x fs-1"></i>
        <p class="mt-2">No hay funciones programadas.</p>
    </div>
    @endforelse
</div>
@endsection
