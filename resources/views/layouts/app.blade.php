<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CinePlus')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ══ Variables propias ══ */
        :root {
            --cp-rojo:   #e50914;
            --cp-oscuro: #141414;
            --cp-gris:   #1f1f1f;
            --cp-texto:  #f5f5f1;

            /* ── Sobreescribir Bootstrap al modo oscuro ── */
            --bs-body-color:                #f5f5f1;
            --bs-body-color-rgb:            245,245,241;
            --bs-body-bg:                   #141414;
            --bs-body-bg-rgb:               20,20,20;
            --bs-secondary-color:           #999;
            --bs-secondary-color-rgb:       153,153,153;
            --bs-secondary-bg:              #2a2a2a;
            --bs-secondary-bg-rgb:          42,42,42;
            --bs-tertiary-bg:               #333;
            --bs-tertiary-bg-rgb:           51,51,51;
            --bs-emphasis-color:            #fff;
            --bs-emphasis-color-rgb:        255,255,255;
            --bs-border-color:              #444;
            --bs-border-color-translucent:  rgba(255,255,255,.1);
            --bs-heading-color:             inherit;
            --bs-link-color:                #e50914;
            --bs-link-hover-color:          #c40812;
            --bs-link-color-rgb:            229,9,20;
            --bs-code-color:                #e09090;
            /* Tarjetas */
            --bs-card-color:                #f5f5f1;
            --bs-card-bg:                   #1f1f1f;
            --bs-card-cap-bg:               rgba(255,255,255,.03);
            --bs-card-border-color:         #333;
            --bs-card-title-color:          #f5f5f1;
            /* Tablas */
            --bs-table-color:               #f5f5f1;
            --bs-table-bg:                  transparent;
            --bs-table-border-color:        #2a2a2a;
            --bs-table-striped-bg:          rgba(255,255,255,.04);
            --bs-table-hover-bg:            rgba(255,255,255,.06);
            --bs-table-active-bg:           rgba(255,255,255,.1);
            /* Dropdowns */
            --bs-dropdown-bg:               #1f1f1f;
            --bs-dropdown-border-color:     #444;
            --bs-dropdown-link-color:       #f5f5f1;
            --bs-dropdown-link-hover-bg:    #2a2a2a;
            --bs-dropdown-link-hover-color: #fff;
            --bs-dropdown-divider-bg:       #333;
            --bs-dropdown-header-color:     #999;
            /* Modales */
            --bs-modal-bg:                  #1f1f1f;
            --bs-modal-color:               #f5f5f1;
            --bs-modal-border-color:        #444;
            --bs-modal-header-border-color: #333;
            --bs-modal-footer-border-color: #333;
            /* Input group */
            --bs-input-group-addon-bg:           #333;
            --bs-input-group-addon-color:        #ccc;
            --bs-input-group-addon-border-color: #444;
            /* Paginación */
            --bs-pagination-bg:             #1f1f1f;
            --bs-pagination-border-color:   #444;
            --bs-pagination-color:          #f5f5f1;
            --bs-pagination-hover-bg:       #2a2a2a;
            --bs-pagination-hover-color:    #fff;
            --bs-pagination-active-bg:      #e50914;
            --bs-pagination-disabled-bg:    #1a1a1a;
            --bs-pagination-disabled-color: #555;
            /* List group */
            --bs-list-group-bg:             #1f1f1f;
            --bs-list-group-border-color:   #333;
            --bs-list-group-color:          #f5f5f1;
        }

        body {
            background-color: var(--cp-oscuro);
            color: var(--cp-texto);
            font-family: 'Segoe UI', sans-serif;
        }

        /* ─── Autofill oscuro (Chrome/Edge/Safari) ─── */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #2a2a2a inset !important;
            -webkit-text-fill-color: #f5f5f1 !important;
            caret-color: #f5f5f1;
        }

        /* ─── Navbar ─── */
        .navbar-cineplus {
            background-color: rgba(20,20,20,.97);
            border-bottom: 2px solid var(--cp-rojo);
        }
        .navbar-brand span { color: var(--cp-rojo); font-weight: 800; font-size: 1.6rem; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28245,245,241,.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .nav-link { color: var(--cp-texto) !important; transition: color .2s; }
        .nav-link:hover, .nav-link.active { color: var(--cp-rojo) !important; }
        .btn-cine { background: var(--cp-rojo); color: #fff; border: none; }
        .btn-cine:hover { background: #c40812; color: #fff; }

        /* ─── Cards ─── */
        .card-pelicula {
            background: var(--cp-gris);
            border: 1px solid #333;
            border-radius: 10px;
            overflow: hidden;
            transition: transform .3s, box-shadow .3s;
        }
        .card-pelicula:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(229,9,20,.35);
        }
        .card-pelicula img { height: 320px; object-fit: cover; width: 100%; }
        .badge-rango { font-size: .7rem; }

        /* ─── Hero ─── */
        .hero {
            background: linear-gradient(135deg, #0d0d0d 0%, #1a0000 60%, #0d0d0d 100%);
            padding: 90px 0 70px;
            border-bottom: 2px solid var(--cp-rojo);
        }
        .hero h1 { font-size: 3rem; font-weight: 900; }
        .hero h1 span { color: var(--cp-rojo); }

        /* ─── Footer ─── */
        footer { background: #0a0a0a; border-top: 2px solid #222; color: #888; }

        /* ─── Forms ─── */
        .form-control, .form-select {
            background: #2a2a2a;
            border: 1px solid #444;
            color: var(--cp-texto);
        }
        .form-control:focus, .form-select:focus {
            background: #333;
            border-color: var(--cp-rojo);
            color: var(--cp-texto);
            box-shadow: 0 0 0 .2rem rgba(229,9,20,.25);
        }
        .form-control::placeholder,
        .form-select::placeholder { color: #777; }
        .form-label  { color: #ccc; font-weight: 500; }
        .form-check-label { color: #ccc; }
        .form-check-input {
            background-color: #2a2a2a;
            border-color: #555;
        }
        .form-check-input:checked {
            background-color: var(--cp-rojo);
            border-color: var(--cp-rojo);
        }
        .input-group-text {
            background: #333;
            border-color: #444;
            color: #ccc;
        }
        select option { background: #2a2a2a; color: #f5f5f1; }

        /* ─── Alerts oscuros ─── */
        .alert-success {
            background: rgba(25,135,84,.2);
            border-color: rgba(25,135,84,.4);
            color: #75b798;
        }
        .alert-danger {
            background: rgba(220,53,69,.2);
            border-color: rgba(220,53,69,.4);
            color: #ea868f;
        }
        .alert-warning {
            background: rgba(255,193,7,.2);
            border-color: rgba(255,193,7,.4);
            color: #ffda6a;
        }
        .alert-info {
            background: rgba(13,202,240,.2);
            border-color: rgba(13,202,240,.4);
            color: #6edff6;
        }
        .alert { color: inherit; }
        .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }

        /* ─── Tablas ─── */
        .table, .table > :not(caption) > * > * {
            color: #f5f5f1;
            border-color: #2a2a2a;
        }
        .table th { background: #111 !important; border-color: #333; }
        .table-hover > tbody > tr:hover > * { color: #fff; }

        /* ─── Panel Admin (si se usa dentro de app layout) ─── */
        .sidebar { background: var(--cp-gris); min-height: 100vh; border-right: 2px solid #2a2a2a; }
        .sidebar .nav-link { padding: .6rem 1.2rem; border-radius: 6px; }
        .sidebar .nav-link:hover { background: rgba(229,9,20,.15); }
        .sidebar .nav-link.active { background: var(--cp-rojo); color: #fff !important; }

        /* ─── Asientos ─── */
        .asiento {
            width: 38px; height: 38px;
            border-radius: 6px 6px 0 0;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 700;
            cursor: pointer; transition: .2s; margin: 3px;
        }
        .asiento.disponible   { background: #2d6a4f; color: #fff; }
        .asiento.disponible:hover { background: #40916c; }
        .asiento.ocupado      { background: #555; color: #999; cursor: not-allowed; }
        .asiento.seleccionado { background: var(--cp-rojo); color: #fff; }
        .pantalla {
            background: linear-gradient(to bottom, #888, #555);
            height: 8px; border-radius: 50% 50% 0 0 / 8px;
            margin: 0 auto 30px; width: 70%;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════ NAVBAR ═══════ --}}
<nav class="navbar navbar-expand-lg navbar-cineplus sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <span>Cine</span>Plus
        </a>
        <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-fill"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('peliculas.*') ? 'active' : '' }}" href="{{ route('peliculas.index') }}">
                        <i class="bi bi-film"></i> Películas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('horarios.*') ? 'active' : '' }}" href="{{ route('horarios.index') }}">
                        <i class="bi bi-calendar3"></i> Cartelera
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sucursales.*') ? 'active' : '' }}" href="{{ route('sucursales.index') }}">
                        <i class="bi bi-geo-alt-fill"></i> Sucursales
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                @if(session('cliente'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            {{ session('cliente')['nombre_cliente'] }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('cliente.reservas') }}">
                                <i class="bi bi-ticket-detailed"></i> Mis Reservas
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('cliente.logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @elseif(session('admin'))
                    <li class="nav-item">
                        <a class="btn btn-cine btn-sm" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Panel Admin
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-outline-secondary btn-sm" type="submit">
                                <i class="bi bi-box-arrow-right"></i> Salir
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cliente.login') }}">
                            <i class="bi bi-person"></i> Iniciar sesión
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-cine btn-sm" href="{{ route('cliente.register') }}">
                            Registrarse
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

{{-- ═══════ ALERTAS FLASH ═══════ --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible m-0 rounded-0 text-center py-2" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible m-0 rounded-0 text-center py-2" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ═══════ CONTENIDO ═══════ --}}
@yield('content')

{{-- ═══════ FOOTER ═══════ --}}
<footer class="py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1">
            <strong style="color:var(--cp-rojo)">Cine</strong><strong>Plus</strong>
            &mdash; Tu experiencia cinematográfica en El Salvador
        </p>
        <small>© {{ date('Y') }} CinePlus. Todos los derechos reservados.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- ═══════ MODAL DE CONFIRMACIÓN GLOBAL ═══════ --}}
<div class="modal fade" id="cpConfirmModal" tabindex="-1" aria-labelledby="cpConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1f1f1f;border:1px solid #444">
            <div class="modal-header" style="border-color:#333">
                <h5 class="modal-title fw-bold" id="cpConfirmTitle">Confirmar acción</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary mb-0" id="cpConfirmBody">¿Estás seguro?</p>
            </div>
            <div class="modal-footer" style="border-color:#333">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>No, volver
                </button>
                <button type="button" class="btn btn-danger" id="cpConfirmOk">
                    <i class="bi bi-check-lg me-1"></i>Sí, confirmar
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
        modalEl.querySelector('#cpConfirmTitle').textContent = btn.dataset.cpTitle   || 'Confirmar acción';
        modalEl.querySelector('#cpConfirmBody').textContent  = btn.dataset.cpConfirm || '¿Estás seguro?';
        const okBtn = modalEl.querySelector('#cpConfirmOk');
        okBtn.className  = 'btn ' + (btn.dataset.cpBtn     || 'btn-danger');
        okBtn.innerHTML  = '<i class="bi bi-check-lg me-1"></i>' + (btn.dataset.cpBtnText || 'Sí, confirmar');
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
