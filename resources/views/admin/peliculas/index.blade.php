@extends('layouts.admin')

@section('title', 'Películas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Películas</h3>
        <small class="text-secondary">Gestión del catálogo — {{ $peliculas->count() }} película(s) registradas</small>
    </div>
    <a href="{{ route('admin.peliculas.create') }}" class="btn btn-cine">
        <i class="bi bi-plus-lg me-1"></i>Nueva Película
    </a>
</div>

{{-- Buscador rápido --}}
<div class="card-admin p-3 mb-3">
    <div class="input-group input-group-sm" style="max-width:360px">
        <span class="input-group-text" style="background:#333;border-color:#444">
            <i class="bi bi-search text-secondary"></i>
        </span>
        <input type="text" id="filtroPelicula" class="form-control"
               placeholder="Buscar por nombre, categoría, rango...">
    </div>
</div>

<div class="card-admin p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaPeliculas">
            <thead>
                <tr>
                    <th class="ps-4">Película</th>
                    <th>Duración</th>
                    <th>Categorías</th>
                    <th>Rango</th>
                    <th>Horarios</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peliculas as $p)
                @php
                    $txtFiltro = strtolower(
                        $p->nom_pelicula . ' ' .
                        $p->rango_edad   . ' ' .
                        $p->categorias->pluck('nom_categoria')->implode(' ')
                    );
                @endphp
                <tr data-texto="{{ $txtFiltro }}">
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $p->img ? asset('storage/'.$p->img) : asset('images/no-poster.svg') }}"
                                 width="46" height="62"
                                 class="rounded"
                                 style="object-fit:cover;flex-shrink:0"
                                 alt="{{ $p->nom_pelicula }}"
                                 onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
                            <div>
                                <div class="fw-bold">{{ $p->nom_pelicula }}</div>
                                <small class="text-secondary">{{ Str::limit($p->descripcion, 55) }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-secondary small">
                        <i class="bi bi-clock me-1"></i>{{ $p->duracion }} min
                    </td>
                    <td>
                        @forelse($p->categorias as $c)
                            <span class="badge me-1" style="background:#2a2a2a;color:#aaa">{{ $c->nom_categoria }}</span>
                        @empty
                            <span class="text-secondary small">—</span>
                        @endforelse
                    </td>
                    <td>
                        @php
                            $color = match($p->rango_edad) {
                                'TP'  => 'bg-success',
                                '+7'  => 'bg-info text-dark',
                                '+13' => 'bg-warning text-dark',
                                '+18' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $color }}">{{ $p->rango_edad }}</span>
                    </td>
                    <td>
                        @php $cnt = $p->horarios_count ?? 0 @endphp
                        <span class="badge bg-secondary">{{ $cnt }} func.</span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.peliculas.edit', $p->id_pelicula) }}"
                           class="btn btn-sm btn-outline-warning me-1"
                           title="Editar película">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.peliculas.destroy', $p->id_pelicula) }}"
                              method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                data-cp-confirm="¿Eliminar la película &quot;{{ $p->nom_pelicula }}&quot;? Se eliminarán también sus horarios y datos asociados."
                                data-cp-title="Eliminar película"
                                data-cp-btn-text="Sí, eliminar"
                                title="Eliminar película">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-5">
                        <i class="bi bi-film fs-2 d-block mb-2"></i>
                        No hay películas registradas.
                        <div class="mt-2">
                            <a href="{{ route('admin.peliculas.create') }}" class="btn btn-cine btn-sm">
                                <i class="bi bi-plus-lg me-1"></i>Agregar primera película
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('filtroPelicula').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaPeliculas tbody tr[data-texto]').forEach(tr => {
        tr.style.display = !q || tr.dataset.texto.includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
