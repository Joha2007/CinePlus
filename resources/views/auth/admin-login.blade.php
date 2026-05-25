@extends('layouts.app')

@section('title', 'Acceso Administrador — CinePlus')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="card" style="background:var(--cp-gris);border:1px solid var(--cp-rojo);border-radius:12px">
                <div class="card-body p-4 p-md-5">

                    {{-- Encabezado --}}
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <span style="
                                display:inline-flex;align-items:center;justify-content:center;
                                width:64px;height:64px;border-radius:50%;
                                background:rgba(229,9,20,.12);border:1px solid rgba(229,9,20,.3)">
                                <i class="bi bi-shield-lock fs-2 text-danger"></i>
                            </span>
                        </div>
                        <h4 class="fw-bold mb-1">Panel de Administración</h4>
                        <p class="text-secondary small mb-0">Acceso exclusivo para administradores</p>
                    </div>

                    {{-- Alerta de error general (sesión) --}}
                    @if(session('error'))
                    <div class="alert alert-danger py-2 small mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
                    </div>
                    @endif

                    <form action="{{ route('admin.login.post') }}" method="POST" id="adminLoginForm" novalidate>
                        @csrf

                        {{-- Correo --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="correo_adm">Correo institucional</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#333;border-color:#444;color:#888">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email"
                                       id="correo_adm"
                                       name="correo_adm"
                                       class="form-control @error('correo_adm') is-invalid @enderror"
                                       value="{{ old('correo_adm') }}"
                                       placeholder="admin@cineplus.com"
                                       autocomplete="email"
                                       autofocus
                                       required>
                                @error('correo_adm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Contraseña --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="contrasena_adm">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#333;border-color:#444;color:#888">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password"
                                       id="contrasena_adm"
                                       name="contrasena_adm"
                                       class="form-control @error('contrasena_adm') is-invalid @enderror"
                                       placeholder="••••••••"
                                       autocomplete="current-password"
                                       required>
                                <button type="button"
                                        class="input-group-text"
                                        id="togglePassword"
                                        title="Mostrar/ocultar contraseña"
                                        style="background:#333;border-color:#444;color:#888;cursor:pointer">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                                @error('contrasena_adm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Botón --}}
                        <button type="submit" class="btn btn-cine w-100 fw-bold py-2" id="btnSubmit">
                            <i class="bi bi-shield-check me-2"></i>Ingresar al panel
                        </button>
                    </form>

                    <hr style="border-color:#2a2a2a;margin:1.5rem 0 1rem">

                    <div class="text-center">
                        <a href="{{ route('home') }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-secondary">
                    ¿Eres cliente?
                    <a href="{{ route('cliente.login') }}" class="text-danger text-decoration-none">Inicia sesión aquí</a>
                </small>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle mostrar/ocultar contraseña
document.getElementById('togglePassword').addEventListener('click', function () {
    const input   = document.getElementById('contrasena_adm');
    const icon    = document.getElementById('eyeIcon');
    const visible = input.type === 'text';
    input.type    = visible ? 'password' : 'text';
    icon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
    this.style.color = visible ? '#888' : 'var(--cp-rojo)';
});

// Estado de carga al enviar
document.getElementById('adminLoginForm').addEventListener('submit', function () {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verificando...';
});
</script>
@endpush
@endsection
