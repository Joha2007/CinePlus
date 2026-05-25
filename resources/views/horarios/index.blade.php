@extends('layouts.app')

@section('title', 'Cartelera — CinePlus')

@section('content')
<div class="container py-5">

    {{-- Encabezado --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">📅 Cartelera</h2>
        <p class="text-secondary mb-0">Funciones disponibles · {{ $horarios->count() }} función(es) programadas</p>
    </div>

    @if($horarios->isEmpty())
    <div class="text-center text-secondary py-5">
        <i class="bi bi-calendar-x fs-1 d-block mb-3 text-danger opacity-50"></i>
        <h5 class="text-secondary">No hay funciones programadas</h5>
        <p class="small">Vuelve pronto para ver la cartelera actualizada.</p>
        <a href="{{ route('peliculas.index') }}" class="btn btn-outline-danger mt-2">
            <i class="bi bi-film me-2"></i>Ver Películas
        </a>
    </div>
    @else

    {{-- ═══ TABS POR SUCURSAL ═══ --}}
    <ul class="nav nav-pills mb-4 gap-2 flex-wrap" id="sucursalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active tab-suc" id="tab-todas"
                    data-bs-toggle="pill" data-bs-target="#todas"
                    type="button" role="tab">
                <i class="bi bi-collection me-1"></i>Todas
                <span class="badge bg-secondary ms-1">{{ $horarios->count() }}</span>
            </button>
        </li>
        @foreach($sucursales as $suc)
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-suc" id="tab-suc-{{ $suc->id_suc }}"
                    data-bs-toggle="pill" data-bs-target="#suc-{{ $suc->id_suc }}"
                    type="button" role="tab">
                <i class="bi bi-building me-1"></i>{{ $suc->nombre_suc }}
                <span class="badge bg-secondary ms-1">{{ $porSucursal->get($suc->id_suc, collect())->count() }}</span>
            </button>
        </li>
        @endforeach
    </ul>

    {{-- ═══ CONTENIDO DE TABS ═══ --}}
    <div class="tab-content" id="sucursalTabsContent">

        {{-- Tab Todas --}}
        <div class="tab-pane fade show active" id="todas" role="tabpanel">
            @foreach($sucursales as $suc)
            @php $horariosSuc = $porSucursal->get($suc->id_suc, collect()); @endphp
            @if($horariosSuc->isNotEmpty())
            {{-- Separador de sucursal --}}
            <div class="sucursal-header mb-3 mt-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="suc-icon">
                        <i class="bi bi-building-fill text-danger"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white">{{ $suc->nombre_suc }}</h5>
                        <small class="text-secondary">
                            <i class="bi bi-geo-alt me-1"></i>{{ $suc->dir_suc }}
                            &nbsp;·&nbsp;
                            {{ $horariosSuc->count() }} función(es)
                        </small>
                    </div>
                </div>
            </div>
            @foreach($horariosSuc as $horario)
                @include('horarios._card', ['horario' => $horario])
            @endforeach
            @endif
            @endforeach
        </div>

        {{-- Tabs individuales por sucursal --}}
        @foreach($sucursales as $suc)
        @php $horariosSuc = $porSucursal->get($suc->id_suc, collect()); @endphp
        <div class="tab-pane fade" id="suc-{{ $suc->id_suc }}" role="tabpanel">
            {{-- Info de sucursal --}}
            <div class="suc-info-banner mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="suc-icon-lg">
                        <i class="bi bi-building-fill fs-3 text-danger"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $suc->nombre_suc }}</h4>
                        <div class="d-flex flex-wrap gap-3 text-secondary small mt-1">
                            <span><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $suc->dir_suc }}</span>
                            <span><i class="bi bi-telephone me-1 text-danger"></i>{{ $suc->contacto_suc }}</span>
                            <span><i class="bi bi-film me-1 text-danger"></i>{{ $horariosSuc->count() }} función(es) disponibles</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($horariosSuc->isEmpty())
                <div class="text-center text-secondary py-5">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                    No hay funciones en esta sucursal por ahora.
                </div>
            @else
                @foreach($horariosSuc as $horario)
                    @include('horarios._card', ['horario' => $horario])
                @endforeach
            @endif
        </div>
        @endforeach

    </div>
    @endif

</div>

<style>
/* ─── Tabs de sucursal ─── */
.tab-suc {
    background: #1f1f1f;
    border: 1px solid #333;
    color: #ccc;
    border-radius: 8px;
    font-size: .85rem;
    padding: .45rem 1rem;
    transition: background .2s, color .2s, border-color .2s;
}
.tab-suc:hover {
    background: #2a2a2a;
    color: #fff;
    border-color: #555;
}
.tab-suc.active {
    background: var(--cp-rojo) !important;
    color: #fff !important;
    border-color: transparent !important;
}

/* ─── Separador de sucursal en "Todas" ─── */
.sucursal-header {
    background: linear-gradient(90deg, rgba(229,9,20,.12) 0%, transparent 100%);
    border-left: 3px solid var(--cp-rojo);
    border-radius: 0 8px 8px 0;
    padding: .75rem 1.2rem;
}
.suc-icon {
    width: 36px; height: 36px;
    background: rgba(229,9,20,.15);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}

/* ─── Banner info sucursal individual ─── */
.suc-info-banner {
    background: #1f1f1f;
    border: 1px solid #333;
    border-left: 4px solid var(--cp-rojo);
    border-radius: 0 10px 10px 0;
    padding: 1.2rem 1.5rem;
}
.suc-icon-lg {
    width: 52px; height: 52px;
    background: rgba(229,9,20,.12);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}

/* ─── Tarjetas de función ─── */
.horario-card {
    background: var(--cp-gris);
    border: 1px solid #2a2a2a;
    border-radius: 10px;
    margin-bottom: .75rem;
    transition: border-color .2s, transform .1s;
    overflow: hidden;
}
.horario-card:hover {
    border-color: #444;
    transform: translateY(-1px);
}
.horario-card .fecha-badge {
    background: rgba(229,9,20,.12);
    border-right: 1px solid #2a2a2a;
    min-width: 72px;
    text-align: center;
    padding: 1rem .75rem;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.horario-card .fecha-badge .mes {
    font-size: .7rem; font-weight: 700; color: var(--cp-rojo); text-transform: uppercase;
}
.horario-card .fecha-badge .dia {
    font-size: 1.8rem; font-weight: 900; line-height: 1; color: #fff;
}
.horario-card .fecha-badge .dow {
    font-size: .65rem; color: #aaa; text-transform: capitalize;
}
</style>
@endsection
