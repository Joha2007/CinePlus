@extends('layouts.app')

@section('title', 'Películas — CinePlus')

@section('content')
<div class="container py-5">

    <h2 class="fw-bold mb-1">🎬 Cartelera</h2>
    <p class="text-secondary mb-4">Todas las películas disponibles en CinePlus</p>

    {{-- Filtro por categoría --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('peliculas.index') }}"
           class="btn btn-sm {{ !request('categoria') ? 'btn-cine' : 'btn-outline-secondary' }}">
            Todas
        </a>
        @foreach($categorias as $cat)
        <a href="{{ route('peliculas.index', ['categoria' => $cat->id_categoria]) }}"
           class="btn btn-sm {{ request('categoria') == $cat->id_categoria ? 'btn-cine' : 'btn-outline-secondary' }}">
            {{ $cat->nom_categoria }}
        </a>
        @endforeach
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
                        @foreach($pelicula->categorias as $cat)
                            <span class="badge me-1" style="background:#2a2a2a;color:#aaa;font-size:.65rem">
                                {{ $cat->nom_categoria }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-secondary small" style="line-height:1.4">
                        {{ Str::limit($pelicula->descripcion, 80) }}
                    </p>
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
            <p class="mt-2">No hay películas en esta categoría.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
