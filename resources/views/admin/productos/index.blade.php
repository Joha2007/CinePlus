@extends('layouts.admin')
@section('title', 'Dulcería')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dulcería — Productos</h3>
        <small class="text-secondary">Gestión de snacks y bebidas</small>
    </div>
    <a href="{{ route('admin.productos.create') }}" class="btn btn-cine">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
    </a>
</div>

{{-- Resumen rápido --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card-admin p-3 text-center">
            <div class="fw-bold fs-4 text-white">{{ $productos->count() }}</div>
            <small class="text-secondary">Productos totales</small>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card-admin p-3 text-center">
            <div class="fw-bold fs-4 text-success">{{ $productos->where('stock', '>', 0)->count() }}</div>
            <small class="text-secondary">Con stock</small>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card-admin p-3 text-center">
            <div class="fw-bold fs-4 text-danger">{{ $productos->where('stock', 0)->count() }}</div>
            <small class="text-secondary">Sin stock</small>
        </div>
    </div>
</div>

<div class="card-admin p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width:50px"></th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Administrador</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $p)
                <tr>
                    <td class="ps-4">
                        <img src="{{ $p->img ? asset('storage/'.$p->img) : asset('images/no-poster.svg') }}"
                             width="44" height="44"
                             class="rounded object-fit-cover"
                             style="object-fit:cover"
                             alt="{{ $p->nom_productos }}"
                             onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $p->nom_productos }}</div>
                        <small class="text-secondary">{{ Str::limit($p->descripcion, 55) }}</small>
                    </td>
                    <td class="fw-bold text-danger">${{ number_format($p->precio_producto, 2) }}</td>
                    <td>
                        @if($p->stock === 0)
                            <span class="badge bg-danger">Sin stock</span>
                        @elseif($p->stock <= 5)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $p->stock }} uds
                            </span>
                        @else
                            <span class="badge bg-success">{{ $p->stock }} uds</span>
                        @endif
                    </td>
                    <td>
                        <div class="small">{{ $p->administrador->nombre_adm ?? '—' }}</div>
                        <small class="text-secondary">{{ $p->administrador->sucursal->nombre_suc ?? '' }}</small>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.productos.edit', $p->id_producto) }}"
                           class="btn btn-sm btn-outline-warning me-1"
                           title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.productos.destroy', $p->id_producto) }}"
                              method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                data-cp-confirm="¿Eliminar el producto &quot;{{ $p->nom_productos }}&quot;? Esta acción no se puede deshacer."
                                data-cp-title="Eliminar producto"
                                data-cp-btn-text="Sí, eliminar"
                                title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-5">
                        <i class="bi bi-cup-straw fs-2 d-block mb-2"></i>
                        No hay productos registrados.
                        <div class="mt-2">
                            <a href="{{ route('admin.productos.create') }}" class="btn btn-cine btn-sm">
                                <i class="bi bi-plus-lg me-1"></i>Agregar primero
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
