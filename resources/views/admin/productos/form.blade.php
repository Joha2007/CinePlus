@extends('layouts.admin')

@section('title', isset($producto) ? 'Editar Producto' : 'Nuevo Producto')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h3 class="fw-bold mb-0">
            {{ isset($producto) ? 'Editar Producto' : 'Nuevo Producto' }}
        </h3>
        <small class="text-secondary">Dulcería — Snacks y bebidas</small>
    </div>
</div>

<div class="row g-4" style="max-width:900px">
    <div class="col-md-8">
        <div class="card-admin p-4">
            <form action="{{ isset($producto) ? route('admin.productos.update', $producto->id_producto) : route('admin.productos.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="productoForm">
                @csrf
                @if(isset($producto)) @method('PUT') @endif

                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
                @endif

                <div class="row g-3">
                    {{-- Nombre --}}
                    <div class="col-12">
                        <label class="form-label" for="nom_productos">Nombre del producto *</label>
                        <input type="text"
                               id="nom_productos"
                               name="nom_productos"
                               class="form-control @error('nom_productos') is-invalid @enderror"
                               value="{{ old('nom_productos', $producto->nom_productos ?? '') }}"
                               placeholder="Ej: Palomitas grandes, Refresco de cola..."
                               maxlength="100"
                               required autofocus>
                        @error('nom_productos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="col-12">
                        <label class="form-label" for="descripcion">Descripción</label>
                        <textarea id="descripcion"
                                  name="descripcion"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  rows="2"
                                  maxlength="500"
                                  placeholder="Breve descripción del producto (opcional)">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Precio y Stock --}}
                    <div class="col-md-6">
                        <label class="form-label" for="precio_producto">Precio ($) *</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#333;border-color:#444;color:#888">$</span>
                            <input type="number"
                                   id="precio_producto"
                                   name="precio_producto"
                                   class="form-control @error('precio_producto') is-invalid @enderror"
                                   value="{{ old('precio_producto', $producto->precio_producto ?? '') }}"
                                   step="0.01" min="0.01"
                                   placeholder="2.50"
                                   required>
                            @error('precio_producto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="stock">Stock (unidades) *</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#333;border-color:#444;color:#888">
                                <i class="bi bi-boxes"></i>
                            </span>
                            <input type="number"
                                   id="stock"
                                   name="stock"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', $producto->stock ?? 0) }}"
                                   min="0"
                                   required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Imagen --}}
                    <div class="col-12">
                        <label class="form-label">Imagen del producto</label>

                        @if(isset($producto) && $producto->img)
                        <div class="mb-2 d-flex align-items-center gap-3">
                            <img id="imgPreview"
                                 src="{{ asset('storage/'.$producto->img) }}"
                                 height="80"
                                 class="rounded"
                                 alt="Imagen actual"
                                 onerror="this.onerror=null;this.style.display='none'">
                            <small class="text-secondary">Imagen actual — sube una nueva para reemplazarla.</small>
                        </div>
                        @else
                        <div class="mb-2">
                            <img id="imgPreview" src="" height="80" class="rounded d-none" alt="Vista previa">
                        </div>
                        @endif

                        <input type="file"
                               id="imgInput"
                               name="img"
                               class="form-control @error('img') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/webp,image/gif">
                        <small class="text-secondary">JPG, PNG, WEBP — máx. 5 MB. Dejar vacío para mantener la actual.</small>
                        @error('img')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Acciones --}}
                    <div class="col-12 pt-2">
                        <button type="submit" class="btn btn-cine px-5" id="btnGuardar">
                            <i class="bi bi-save me-2"></i>
                            {{ isset($producto) ? 'Actualizar producto' : 'Guardar producto' }}
                        </button>
                        <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary ms-2">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Panel lateral: ayuda / vista previa --}}
    <div class="col-md-4">
        <div class="card-admin p-4">
            <h6 class="fw-bold mb-3 text-secondary">
                <i class="bi bi-eye me-2"></i>Vista previa
            </h6>
            <div class="text-center">
                <img id="cardImgPreview"
                     src="{{ isset($producto) && $producto->img ? asset('storage/'.$producto->img) : asset('images/no-poster.svg') }}"
                     class="rounded mb-2"
                     style="width:100%;max-width:180px;height:140px;object-fit:cover"
                     alt="Vista previa"
                     onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
                <div class="fw-bold mt-2" id="previewNombre" style="color:#f5f5f1">
                    {{ $producto->nom_productos ?? 'Nombre del producto' }}
                </div>
                <div class="text-danger fw-bold fs-5 mt-1" id="previewPrecio">
                    ${{ number_format($producto->precio_producto ?? 0, 2) }}
                </div>
                <div class="text-secondary small mt-1" id="previewStock">
                    Stock: {{ $producto->stock ?? 0 }}
                </div>
            </div>
        </div>

        <div class="card-admin p-4 mt-3">
            <h6 class="fw-bold mb-3 text-secondary">
                <i class="bi bi-info-circle me-2"></i>Notas
            </h6>
            <ul class="text-secondary small mb-0 ps-3">
                <li>El precio debe estar en dólares ($).</li>
                <li>Stock 0 oculta el producto en el menú del cliente.</li>
                <li>La imagen se recomienda en proporción 4:3.</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Vista previa de imagen al seleccionar archivo
document.getElementById('imgInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    document.getElementById('imgPreview').src = url;
    document.getElementById('imgPreview').classList.remove('d-none');
    document.getElementById('cardImgPreview').src = url;
});

// Vista previa en tiempo real: nombre
document.getElementById('nom_productos').addEventListener('input', function () {
    document.getElementById('previewNombre').textContent = this.value || 'Nombre del producto';
});

// Vista previa en tiempo real: precio
document.getElementById('precio_producto').addEventListener('input', function () {
    const val = parseFloat(this.value) || 0;
    document.getElementById('previewPrecio').textContent = '$' + val.toFixed(2);
});

// Vista previa en tiempo real: stock
document.getElementById('stock').addEventListener('input', function () {
    document.getElementById('previewStock').textContent = 'Stock: ' + (this.value || '0');
});

// Loading al guardar
document.getElementById('productoForm').addEventListener('submit', function () {
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>
@endpush
@endsection
