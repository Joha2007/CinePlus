@extends('layouts.app')
@section('title', 'Sucursales — CinePlus')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-1">📍 Nuestras Sucursales</h2>
    <p class="text-secondary mb-4">Encuéntranos en distintos puntos de El Salvador</p>
    <div class="row g-4">
        @foreach($sucursales as $s)
        <div class="col-md-4">
            <div class="card h-100" style="background:var(--cp-gris);border:1px solid #333">
                <div class="card-body p-4">
                    <i class="bi bi-building fs-1 text-danger mb-3 d-block"></i>
                    <h4 class="fw-bold">{{ $s->nombre_suc }}</h4>
                    <p class="text-secondary mb-1"><i class="bi bi-geo-alt me-2"></i>{{ $s->dir_suc }}</p>
                    <p class="text-secondary mb-3"><i class="bi bi-telephone me-2"></i>{{ $s->contacto_suc }}</p>
                    <hr style="border-color:#333">
                    <div class="d-flex justify-content-between text-secondary small">
                        <span><i class="bi bi-grid-3x3-gap me-1"></i>{{ $s->salas->count() }} salas</span>
                        <span><i class="bi bi-people me-1"></i>{{ $s->administradores->count() }} admins</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
