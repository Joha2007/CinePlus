@extends('layouts.app')

@section('title', 'Registrarse — CinePlus')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card" style="background:var(--cp-gris);border:1px solid #333">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold"><span style="color:var(--cp-rojo)">Cine</span>Plus</h3>
                        <p class="text-secondary">Crea tu cuenta gratuita</p>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('cliente.register.post') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre_cliente" class="form-control"
                                       value="{{ old('nombre_cliente') }}" placeholder="Ana" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido_cliente" class="form-control"
                                       value="{{ old('apellido_cliente') }}" placeholder="García" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="correo_cli" class="form-control"
                                       value="{{ old('correo_cli') }}" placeholder="tucorreo@gmail.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Edad</label>
                                <input type="number" name="edad_cli" class="form-control"
                                       value="{{ old('edad_cli') }}" min="18" max="85" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="contacto_cli" class="form-control"
                                       value="{{ old('contacto_cli') }}" placeholder="7500-0000" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="contrasena_cli" class="form-control"
                                       placeholder="Mínimo 6 caracteres" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmar contraseña</label>
                                <input type="password" name="contrasena_cli_confirmation" class="form-control"
                                       placeholder="Repite tu contraseña" required>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-cine w-100 fw-bold">
                                    <i class="bi bi-person-check me-2"></i>Crear cuenta
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr style="border-color:#444">
                    <div class="text-center">
                        <small class="text-secondary">¿Ya tienes cuenta?</small>
                        <a href="{{ route('cliente.login') }}" class="text-danger ms-1">Iniciar sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
