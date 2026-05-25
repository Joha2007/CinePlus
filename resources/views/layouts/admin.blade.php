<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — CinePlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --cp-rojo:#e50914; --cp-oscuro:#141414; --cp-gris:#1f1f1f; }
        body { background:var(--cp-oscuro); color:#f5f5f1; }
        .sidebar { background:var(--cp-gris); min-height:100vh; width:240px; position:fixed;
                   border-right:2px solid #2a2a2a; padding-top:1rem; }
        .sidebar .brand { color:var(--cp-rojo); font-weight:900; font-size:1.4rem; padding:1rem 1.2rem .5rem; }
        .sidebar .nav-link { color:#ccc; padding:.6rem 1.2rem; border-radius:6px; margin:.1rem .5rem; }
        .sidebar .nav-link:hover { background:rgba(229,9,20,.15); color:#fff; }
        .sidebar .nav-link.active { background:var(--cp-rojo); color:#fff; }
        .sidebar .nav-link i { width:20px; }
        .main-content { margin-left:240px; padding:2rem; }
        .card-admin { background:var(--cp-gris); border:1px solid #2a2a2a; border-radius:10px; }
        .table { color:#f5f5f1; }
        .table th { background:#111; border-color:#333; }
        .table td { border-color:#2a2a2a; }
        .form-control, .form-select { background:#2a2a2a; border:1px solid #444; color:#f5f5f1; }
        .form-control:focus, .form-select:focus { background:#333; border-color:var(--cp-rojo);
            color:#f5f5f1; box-shadow:0 0 0 .2rem rgba(229,9,20,.25); }
        .btn-cine { background:var(--cp-rojo); color:#fff; border:none; }
        .btn-cine:hover { background:#c40812; color:#fff; }
        .stat-card { border-radius:12px; padding:1.5rem; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar d-flex flex-column">
    <div class="brand">Cine<span style="color:#fff">Plus</span></div>
    <small class="text-secondary px-3 mb-3" style="font-size:.75rem">
        {{ session('admin')['nombre_adm'] ?? 'Admin' }}
        — {{ session('admin')['sucursal']['nombre_suc'] ?? '' }}
    </small>

    <nav class="nav flex-column flex-grow-1">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="text-secondary px-3 mt-3 mb-1" style="font-size:.7rem;text-transform:uppercase">Catálogo</div>
        <a class="nav-link {{ request()->routeIs('admin.peliculas.*') ? 'active' : '' }}"
           href="{{ route('admin.peliculas.index') }}">
            <i class="bi bi-film"></i> Películas
        </a>
        <a class="nav-link {{ request()->routeIs('admin.horarios.*') ? 'active' : '' }}"
           href="{{ route('admin.horarios.index') }}">
            <i class="bi bi-calendar3"></i> Horarios
        </a>
        <a class="nav-link {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}"
           href="{{ route('admin.categorias.index') }}">
            <i class="bi bi-tags"></i> Categorías
        </a>
        <div class="text-secondary px-3 mt-3 mb-1" style="font-size:.7rem;text-transform:uppercase">Operaciones</div>
        <a class="nav-link {{ request()->routeIs('admin.salas.*') ? 'active' : '' }}"
           href="{{ route('admin.salas.index') }}">
            <i class="bi bi-grid-3x3-gap"></i> Salas
        </a>
        <a class="nav-link {{ request()->routeIs('admin.reservas.*') ? 'active' : '' }}"
           href="{{ route('admin.reservas.index') }}">
            <i class="bi bi-ticket-detailed"></i> Reservas
        </a>
        <a class="nav-link {{ request()->routeIs('admin.productos.*') ? 'active' : '' }}"
           href="{{ route('admin.productos.index') }}">
            <i class="bi bi-cup-straw"></i> Dulcería
        </a>
        <div class="text-secondary px-3 mt-3 mb-1" style="font-size:.7rem;text-transform:uppercase">Usuarios</div>
        <a class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}"
           href="{{ route('admin.clientes.index') }}">
            <i class="bi bi-people"></i> Clientes
        </a>
    </nav>

    <div class="p-3 mt-auto">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-secondary btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
            </button>
        </form>
    </div>
</div>

{{-- Contenido principal --}}
<div class="main-content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-3">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')
</div>

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
