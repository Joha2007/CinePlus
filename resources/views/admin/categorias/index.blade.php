@extends('layouts.admin')
@section('title', 'Categorías')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Categorías</h3>
</div>
<div class="row g-4">
    <div class="col-md-5">
        <div class="card-admin p-4">
            <h5 class="fw-bold mb-3">Nueva categoría</h5>
            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf
                @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul></div>
                @endif
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nom_categoria" class="form-control"
                           value="{{ old('nom_categoria') }}" placeholder="Ej: Aventura" required>
                </div>
                <button type="submit" class="btn btn-cine w-100">
                    <i class="bi bi-plus-lg me-1"></i>Agregar
                </button>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card-admin p-0 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th class="ps-4">Categoría</th><th>Películas</th><th class="text-end pe-4">Eliminar</th></tr></thead>
                <tbody>
                    @forelse($categorias as $c)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $c->nom_categoria }}</td>
                        <td><span class="badge bg-secondary">{{ $c->peliculas_count }}</span></td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.categorias.destroy', $c->id_categoria) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    data-cp-confirm="¿Eliminar la categoría &quot;{{ $c->nom_categoria }}&quot;? Las películas asociadas perderán esta categoría."
                                    data-cp-title="Eliminar categoría"
                                    data-cp-btn-text="Sí, eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-secondary py-4">No hay categorías.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
