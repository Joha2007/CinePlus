@extends('layouts.app')

@section('title', $pelicula->nom_pelicula . ' — CinePlus')

@section('content')
<div class="container py-5">

    <div class="row g-5">
        {{-- Póster --}}
        <div class="col-md-4">
            <img src="{{ $pelicula->img ? asset('storage/'.$pelicula->img) : asset('images/no-poster.svg') }}"
                 class="img-fluid rounded shadow"
                 alt="{{ $pelicula->nom_pelicula }}"
                 onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
        </div>

        {{-- Info --}}
        <div class="col-md-8">
            <div class="mb-2">
                @foreach($pelicula->categorias as $cat)
                    <span class="badge me-1" style="background:#2a2a2a;color:#aaa">
                        {{ $cat->nom_categoria }}
                    </span>
                @endforeach
            </div>
            <h1 class="fw-bold mb-3">{{ $pelicula->nom_pelicula }}</h1>
            <div class="d-flex gap-3 mb-4 flex-wrap">
                <span class="badge bg-danger fs-6 px-3">{{ $pelicula->rango_edad }}</span>
                <span class="text-secondary"><i class="bi bi-clock me-1"></i>{{ $pelicula->duracion }} minutos</span>
            </div>
            <p class="lead" style="color:#ccc">{{ $pelicula->descripcion }}</p>

            <hr style="border-color:#333">

            {{-- Horarios --}}
            <h4 class="fw-bold mb-3"><i class="bi bi-calendar3 text-danger me-2"></i>Horarios disponibles</h4>
            @if($pelicula->horarios->isEmpty())
                <p class="text-secondary">No hay horarios programados aún.</p>
            @else
            <div class="row g-3">
                @foreach($pelicula->horarios as $horario)
                <div class="col-md-6">
                    <div class="card" style="background:#1f1f1f;border:1px solid #333">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">
                                        {{ \Carbon\Carbon::parse($horario->fecha)->locale('es')->isoFormat('dddd, D MMM YYYY') }}
                                    </h6>
                                    <p class="text-secondary mb-1">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }}
                                    </p>
                                    <small class="text-secondary">
                                        <i class="bi bi-building me-1"></i>
                                        {{ $horario->sala->sucursal->nombre_suc ?? 'N/A' }}
                                        — Sala {{ $horario->sala->num_sala ?? '' }}
                                    </small>
                                </div>
                                <span class="badge bg-secondary">{{ $horario->tec_proyecc }}</span>
                            </div>
                            <div class="mt-3">
                                @if(session('cliente'))
                                    <a href="{{ route('reservas.create', ['horario' => $horario->id_horario]) }}"
                                       class="btn btn-cine btn-sm w-100">
                                        <i class="bi bi-ticket-detailed me-1"></i>Reservar
                                    </a>
                                @else
                                    <a href="{{ route('cliente.login') }}"
                                       class="btn btn-outline-danger btn-sm w-100">
                                        <i class="bi bi-lock me-1"></i>Inicia sesión para reservar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
