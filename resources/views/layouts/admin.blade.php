<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — CinePlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --cp-rojo:    #e50914;
            --cp-rojo-d:  #c40812;
            --cp-oscuro:  #0d0d0d;
            --cp-gris:    #161616;
            --cp-gris2:   #1e1e1e;
            --cp-borde:   #272727;
            --cp-texto:   #e8e8e4;
            --cp-muted:   #888;
            --sidebar-w:  256px;
        }

        * { box-sizing: border-box; }

        body {
            background: var(--cp-oscuro);
            color: var(--cp-texto);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
        }

        /* ══════════════════════ SIDEBAR ══════════════════════ */
        .sidebar {
            background: var(--cp-gris);
            width: var(--sidebar-w);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            border-right: 1px solid var(--cp-borde);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
        }

        /* Marca */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: 1.2rem 1.3rem .9rem;
            border-bottom: 1px solid var(--cp-borde);
            text-decoration: none;
        }
        .sidebar-brand .brand-icon {
            width: 34px; height: 34px;
            background: var(--cp-rojo);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-text {
            font-size: 1.15rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: -.5px;
        }
        .sidebar-brand .brand-text span { color: var(--cp-rojo); }

        /* Perfil del admin */
        .sidebar-profile {
            padding: .85rem 1.3rem;
            border-bottom: 1px solid var(--cp-borde);
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .profile-avatar {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--cp-rojo) 0%, #7c1c21 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800;
            font-size: .85rem;
            color: #fff;
            flex-shrink: 0;
        }
        .profile-info { min-width: 0; }
        .profile-name {
            font-size: .83rem;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .profile-suc {
            font-size: .7rem;
            color: var(--cp-rojo);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: .3rem;
        }
        .profile-suc::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--cp-rojo);
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Navegación */
        .sidebar-nav {
            flex: 1;
            padding: .6rem .75rem;
        }
        .nav-section-title {
            font-size: .63rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--cp-muted);
            padding: .9rem .6rem .35rem;
        }
        .nav-item-cp {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .52rem .75rem;
            border-radius: 7px;
            color: #aaa;
            text-decoration: none;
            font-size: .84rem;
            font-weight: 500;
            margin-bottom: 2px;
            transition: background .15s, color .15s;
            position: relative;
        }
        .nav-item-cp i {
            font-size: .95rem;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }
        .nav-item-cp:hover {
            background: rgba(255,255,255,.06);
            color: #fff;
        }
        .nav-item-cp.active {
            background: rgba(229,9,20,.18);
            color: #fff;
            font-weight: 600;
        }
        .nav-item-cp.active i { color: var(--cp-rojo); }
        .nav-item-cp.active::before {
            content: '';
            position: absolute;
            left: 0; top: 6px; bottom: 6px;
            width: 3px;
            background: var(--cp-rojo);
            border-radius: 0 3px 3px 0;
        }

        /* Botón cerrar sesión */
        .sidebar-footer {
            padding: .75rem;
            border-top: 1px solid var(--cp-borde);
        }
        .btn-logout {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .5rem .75rem;
            border-radius: 7px;
            color: var(--cp-muted);
            text-decoration: none;
            font-size: .82rem;
            width: 100%;
            border: 1px solid var(--cp-borde);
            background: transparent;
            cursor: pointer;
            transition: background .15s, color .15s, border-color .15s;
        }
        .btn-logout:hover {
            background: rgba(229,9,20,.1);
            color: var(--cp-rojo);
            border-color: rgba(229,9,20,.3);
        }

        /* ══════════════════════ CONTENIDO PRINCIPAL ══════════════════════ */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            background: var(--cp-gris);
            border-bottom: 1px solid var(--cp-borde);
            padding: .75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-title {
            font-size: .85rem;
            color: var(--cp-muted);
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .topbar-title .breadcrumb-sep { color: var(--cp-borde); }
        .topbar-title .page-name { color: #fff; font-weight: 600; }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .topbar-badge {
            display: flex;
            align-items: center;
            gap: .4rem;
            background: rgba(229,9,20,.12);
            border: 1px solid rgba(229,9,20,.2);
            border-radius: 20px;
            padding: .25rem .75rem;
            font-size: .75rem;
            color: var(--cp-rojo);
            font-weight: 600;
        }

        /* Área de contenido */
        .content-area {
            flex: 1;
            padding: 2rem;
        }

        /* ══════════════════════ COMPONENTES ══════════════════════ */
        .card-admin {
            background: var(--cp-gris2);
            border: 1px solid var(--cp-borde);
            border-radius: 12px;
        }
        .table {
            color: var(--cp-texto);
        }
        .table th {
            background: rgba(0,0,0,.3);
            border-color: var(--cp-borde);
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--cp-muted);
            padding: .85rem 1rem;
        }
        .table td {
            border-color: var(--cp-borde);
            padding: .85rem 1rem;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover td {
            background: rgba(255,255,255,.03);
        }
        .form-control, .form-select {
            background: #111;
            border: 1px solid #333;
            color: var(--cp-texto);
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background: #1a1a1a;
            border-color: var(--cp-rojo);
            color: var(--cp-texto);
            box-shadow: 0 0 0 3px rgba(229,9,20,.15);
        }
        .form-control::placeholder { color: #555; }
        .form-label { font-size: .83rem; color: #bbb; font-weight: 600; }

        .btn-cine {
            background: var(--cp-rojo);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background .15s, transform .1s;
        }
        .btn-cine:hover {
            background: var(--cp-rojo-d);
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-cine:active { transform: translateY(0); }

        .stat-card {
            border-radius: 12px;
            padding: 1.4rem;
        }

        /* Alertas tipo toast */
        .alert {
            border-radius: 10px;
            border: none;
            font-size: .88rem;
        }
        .alert-success {
            background: rgba(34,197,94,.12);
            border-left: 3px solid #22c55e;
            color: #86efac;
        }
        .alert-danger {
            background: rgba(229,9,20,.12);
            border-left: 3px solid var(--cp-rojo);
            color: #fca5a5;
        }
        .alert-warning {
            background: rgba(234,179,8,.12);
            border-left: 3px solid #eab308;
            color: #fde68a;
        }
        .alert-info {
            background: rgba(6,182,212,.12);
            border-left: 3px solid #06b6d4;
            color: #67e8f9;
        }

        /* Input groups */
        .input-group-text {
            background: #111;
            border-color: #333;
            color: var(--cp-muted);
        }

        /* Badges */
        .badge { font-weight: 600; border-radius: 5px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ══════ SIDEBAR ══════ --}}
@php
    $admin    = session('admin');
    $initials = strtoupper(substr($admin['nombre_adm'] ?? 'A', 0, 1) . substr($admin['apellido_adm'] ?? '', 0, 1));
    $sucNombre = $admin['sucursal']['nombre_suc'] ?? 'Sin sucursal';
@endphp

<aside class="sidebar">

    {{-- Marca --}}
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <div class="brand-icon">🎬</div>
        <div class="brand-text">Cine<span>Plus</span></div>
    </a>

    {{-- Perfil --}}
    <div class="sidebar-profile">
        <div class="profile-avatar">{{ $initials }}</div>
        <div class="profile-info">
            <div class="profile-name">{{ $admin['nombre_adm'] ?? 'Administrador' }}</div>
            <div class="profile-suc">{{ $sucNombre }}</div>
        </div>
    </div>

    {{-- Navegación --}}
    <nav class="sidebar-nav">

        <a class="nav-item-cp {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <div class="nav-section-title">Catálogo</div>
        <a class="nav-item-cp {{ request()->routeIs('admin.peliculas.*') ? 'active' : '' }}"
           href="{{ route('admin.peliculas.index') }}">
            <i class="bi bi-film"></i> Películas
        </a>
        <a class="nav-item-cp {{ request()->routeIs('admin.horarios.*') ? 'active' : '' }}"
           href="{{ route('admin.horarios.index') }}">
            <i class="bi bi-calendar3"></i> Horarios
        </a>
        <a class="nav-item-cp {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}"
           href="{{ route('admin.categorias.index') }}">
            <i class="bi bi-tags"></i> Categorías
        </a>

        <div class="nav-section-title">Mi Sucursal</div>
        <a class="nav-item-cp {{ request()->routeIs('admin.salas.*') ? 'active' : '' }}"
           href="{{ route('admin.salas.index') }}">
            <i class="bi bi-grid-3x3-gap"></i> Salas
        </a>
        <a class="nav-item-cp {{ request()->routeIs('admin.reservas.*') ? 'active' : '' }}"
           href="{{ route('admin.reservas.index') }}">
            <i class="bi bi-ticket-detailed"></i> Reservas
        </a>
        <a class="nav-item-cp {{ request()->routeIs('admin.productos.*') ? 'active' : '' }}"
           href="{{ route('admin.productos.index') }}">
            <i class="bi bi-cup-straw"></i> Dulcería
        </a>


    </nav>

    {{-- Logout --}}
    <div class="sidebar-footer">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>

{{-- ══════ CONTENIDO PRINCIPAL ══════ --}}
<div class="main-content">

    {{-- Topbar --}}
    <div class="topbar">
        <div class="topbar-title">
            <i class="bi bi-house" style="color:var(--cp-muted)"></i>
            <span class="breadcrumb-sep">/</span>
            <span class="page-name">@yield('title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="bi bi-building"></i>
                {{ $sucNombre }}
            </div>
        </div>
    </div>

    {{-- Mensajes flash --}}
    <div class="content-area">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- ═══ MODAL DE CONFIRMACIÓN GLOBAL ═══ --}}
<div class="modal fade" id="cpConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background:#1a1a1a;border:1px solid #333;border-radius:14px">
            <div class="modal-header" style="border-color:#2a2a2a;padding:1.2rem 1.4rem .8rem">
                <h6 class="modal-title fw-bold mb-0" id="cpConfirmTitle">Confirmar acción</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:.8rem 1.4rem 1.2rem">
                <p class="text-secondary mb-0 small" id="cpConfirmBody">¿Estás seguro?</p>
            </div>
            <div class="modal-footer" style="border-color:#2a2a2a;padding:.8rem 1.4rem 1.2rem;gap:.5rem">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-danger" id="cpConfirmOk">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const modalEl = document.getElementById('cpConfirmModal');
    if (!modalEl) return;
    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    let pendingForm = null;

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-cp-confirm]');
        if (!btn) return;
        e.preventDefault();
        pendingForm = btn.closest('form');
        document.getElementById('cpConfirmTitle').textContent = btn.dataset.cpTitle   || 'Confirmar acción';
        document.getElementById('cpConfirmBody').textContent  = btn.dataset.cpConfirm || '¿Estás seguro?';
        const okBtn = document.getElementById('cpConfirmOk');
        okBtn.className = 'btn btn-sm ' + (btn.dataset.cpBtn || 'btn-danger');
        okBtn.textContent = btn.dataset.cpBtnText || 'Sí, confirmar';
        bsModal.show();
    });

    document.getElementById('cpConfirmOk').addEventListener('click', function () {
        bsModal.hide();
        if (pendingForm) pendingForm.submit();
    });
}());
</script>

@stack('scripts')
</body>
</html>
