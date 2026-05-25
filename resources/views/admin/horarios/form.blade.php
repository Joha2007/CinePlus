@extends('layouts.admin')
@section('title', isset($horario) ? 'Editar Horario' : 'Nuevo Horario')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.horarios.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h3 class="fw-bold mb-0">{{ isset($horario) ? 'Editar Horario' : 'Nuevo Horario' }}</h3>
</div>
<div class="card-admin p-4" style="max-width:600px">
    <form action="{{ isset($horario) ? route('admin.horarios.update', $horario->id_horario) : route('admin.horarios.store') }}"
          method="POST">
        @csrf
        @if(isset($horario)) @method('PUT') @endif

        @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul></div>
        @endif

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Película *</label>
                <select name="id_pelicula1" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    @foreach($peliculas as $p)
                    <option value="{{ $p->id_pelicula }}"
                        {{ old('id_pelicula1', $horario->id_pelicula1 ?? '') == $p->id_pelicula ? 'selected' : '' }}>
                        {{ $p->nom_pelicula }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Sala *</label>
                <select name="id_sala2" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    @foreach($salas as $s)
                    <option value="{{ $s->id_sala }}"
                        {{ old('id_sala2', $horario->id_sala2 ?? '') == $s->id_sala ? 'selected' : '' }}>
                        Sala {{ $s->num_sala }} — {{ $s->sucursal->nombre_suc ?? '' }} ({{ $s->capaci_sala }} asientos)
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha *</label>
                <input type="date" name="fecha" class="form-control"
                       value="{{ old('fecha', isset($horario) ? $horario->fecha : '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hora de inicio *</label>
                <input type="time" name="hora_inicio" class="form-control"
                       value="{{ old('hora_inicio', isset($horario) ? \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') : '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tecnología *</label>
                <select name="tec_proyecc" class="form-select" required>
                    @foreach(['2D','3D','IMAX'] as $t)
                    <option value="{{ $t }}"
                        {{ old('tec_proyecc', $horario->tec_proyecc ?? '') == $t ? 'selected' : '' }}>
                        {{ $t }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-cine px-5">
                    <i class="bi bi-save me-2"></i>{{ isset($horario) ? 'Actualizar' : 'Guardar' }}
                </button>
                <a href="{{ route('admin.horarios.index') }}" class="btn btn-outline-secondary ms-2">Cancelar</a>
            </div>
        </div>
    </form>
</div>
@endsection
