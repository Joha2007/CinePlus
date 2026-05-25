@extends('layouts.app')

@section('title', 'Iniciar Sesión — CinePlus')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card" style="background:var(--cp-gris);border:1px solid #333">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold"><span style="color:var(--cp-rojo)">Cine</span>Plus</h3>
                        <p class="text-secondary">Inicia sesión en tu cuenta</p>
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

                    <form action="{{ route('cliente.login.post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="correo_cli" class="form-control"
                                   value="{{ old('correo_cli') }}" placeholder="tucorreo@gmail.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="contrasena_cli" class="form-control"
                                   placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-cine w-100 fw-bold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                        </button>
                    </form>

                    <hr style="border-color:#444">

                    <div class="text-center">
                        <small class="text-secondary">¿No tienes cuenta?</small>
                        <a href="{{ route('cliente.register') }}" class="text-danger ms-1">Regístrate gratis</a>
                    </div>

                    <hr style="border-color:#333">
                    <div class="text-center">
                        <small class="text-secondary">¿Eres administrador?</small>
                        <a href="{{ route('admin.login') }}" class="text-secondary ms-1">Acceso admin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
