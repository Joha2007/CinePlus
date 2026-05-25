@extends('layouts.app')

@section('title', 'CinePlus — Inicio')

@section('content')

{{-- Hero --}}
<section class="hero text-center">
    <div class="container">
        <h1>Bienvenido a <span>CinePlus</span></h1>
        <p class="lead text-secondary mt-3">
            La mejor experiencia cinematográfica de El Salvador.<br>
            Reserva tus entradas en línea fácil y rápido.
        </p>
        <a href="{{ route('peliculas.index') }}" class="btn btn-cine btn-lg mt-3 px-5">
            <i class="bi bi-film me-2"></i>Ver Películas
        </a>
        <a href="{{ route('horarios.index') }}" class="btn btn-outline-light btn-lg mt-3 ms-2 px-5">
            <i class="bi bi-calendar3 me-2"></i>Cartelera
        </a>
    </div>
</section>

{{-- Películas en cartelera --}}
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">🎬 En Cartelera</h2>
            <a href="{{ route('peliculas.index') }}" class="text-danger text-decoration-none">
                Ver todas <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            @forelse($peliculas as $pelicula)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card-pelicula h-100">
                    <img src="{{ $pelicula->img ? asset('storage/'.$pelicula->img) : asset('images/no-poster.svg') }}"
                         alt="{{ $pelicula->nom_pelicula }}"
                         onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">{{ $pelicula->nom_pelicula }}</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-secondary">
                                <i class="bi bi-clock"></i> {{ $pelicula->duracion }} min
                            </small>
                            <span class="badge bg-danger badge-rango">{{ $pelicula->rango_edad }}</span>
                        </div>
                        <div class="mb-2">
                            @foreach($pelicula->categorias->take(2) as $cat)
                                <span class="badge" style="background:#2a2a2a;color:#aaa;font-size:.65rem">
                                    {{ $cat->nom_categoria }}
                                </span>
                            @endforeach
                        </div>
                        <a href="{{ route('peliculas.show', $pelicula->id_pelicula) }}"
                           class="btn btn-cine btn-sm w-100">
                            Ver detalles
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-secondary py-5">
                <i class="bi bi-camera-video-off fs-1"></i>
                <p class="mt-2">No hay películas disponibles.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- Sucursales --}}
<section class="py-5" style="background:var(--cp-gris)">
    <div class="container">
        <h2 class="fw-bold mb-4 text-center">📍 Nuestras Sucursales</h2>
        <div class="row g-4 justify-content-center">
            @foreach($sucursales as $suc)
            <div class="col-md-4">
                <div class="card h-100" style="background:#141414;border:1px solid #333;">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-building fs-1 text-danger mb-3"></i>
                        <h5 class="card-title fw-bold">{{ $suc->nombre_suc }}</h5>
                        <p class="text-secondary mb-1">
                            <i class="bi bi-geo-alt"></i> {{ $suc->dir_suc }}
                        </p>
                        <p class="text-secondary">
                            <i class="bi bi-telephone"></i> {{ $suc->contacto_suc }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
