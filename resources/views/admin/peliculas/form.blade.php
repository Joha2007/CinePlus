@extends('layouts.admin')

@section('title', isset($pelicula) ? 'Editar Película' : 'Nueva Película')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.peliculas.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h3 class="fw-bold mb-0">{{ isset($pelicula) ? 'Editar Película' : 'Nueva Película' }}</h3>
    </div>
</div>

<div class="card-admin p-4" style="max-width:750px">
    <form action="{{ isset($pelicula) ? route('admin.peliculas.update', $pelicula->id_pelicula) : route('admin.peliculas.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($pelicula)) @method('PUT') @endif

        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
        @endif

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Nombre de la película *</label>
                <input type="text" name="nom_pelicula" class="form-control"
                       value="{{ old('nom_pelicula', $pelicula->nom_pelicula ?? '') }}"
                       placeholder="Ej: Deadpool & Wolverine" required>
            </div>

            <div class="col-12">
                <label class="form-label">Descripción *</label>
                <textarea name="descripcion" class="form-control" rows="3"
                          placeholder="Sinopsis de la película..." required>{{ old('descripcion', $pelicula->descripcion ?? '') }}</textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label">Duración (minutos) *</label>
                <input type="number" name="duracion" class="form-control"
                       value="{{ old('duracion', $pelicula->duracion ?? '') }}"
                       min="1" placeholder="127" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Rango de edad *</label>
                <select name="rango_edad" class="form-select" required>
                    @foreach(['TP', '+7', '+13', '+18'] as $r)
                    <option value="{{ $r }}"
                        {{ old('rango_edad', $pelicula->rango_edad ?? '') == $r ? 'selected' : '' }}>
                        {{ $r }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Imagen del póster</label>
                @if(isset($pelicula) && $pelicula->img)
                    <div class="mb-2">
                        <img src="{{ asset('storage/'.$pelicula->img) }}"
                             height="80" class="rounded"
                             alt="Imagen actual"
                             onerror="this.onerror=null;this.style.display='none'">
                        <small class="text-secondary d-block mt-1">Imagen actual</small>
                    </div>
                @endif
                <input type="file" name="img" class="form-control"
                       accept="image/jpeg,image/png,image/webp,image/gif">
                <small class="text-secondary">JPG, PNG, WEBP — máx. 5 MB. Dejar vacío para mantener la actual.</small>
            </div>

            <div class="col-12">
                <label class="form-label">Categorías</label>
                <div class="d-flex flex-wrap gap-3 p-3 rounded" style="background:#2a2a2a">
                    @foreach($categorias as $cat)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="categorias[]" value="{{ $cat->id_categoria }}"
                               id="cat{{ $cat->id_categoria }}"
                               {{ (isset($pelicula) && $pelicula->categorias->contains('id_categoria', $cat->id_categoria)) || in_array($cat->id_categoria, old('categorias', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="cat{{ $cat->id_categoria }}">
                            {{ $cat->nom_categoria }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-cine px-5">
                    <i class="bi bi-save me-2"></i>
                    {{ isset($pelicula) ? 'Actualizar' : 'Guardar' }}
                </button>
                <a href="{{ route('admin.peliculas.index') }}" class="btn btn-outline-secondary ms-2">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
