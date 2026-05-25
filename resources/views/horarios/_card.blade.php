<div class="horario-card">
    <div class="d-flex align-items-stretch">
        {{-- Fecha --}}
        <div class="fecha-badge d-none d-sm-flex">
            <span class="mes">{{ \Carbon\Carbon::parse($horario->fecha)->locale('es')->isoFormat('MMM') }}</span>
            <span class="dia">{{ \Carbon\Carbon::parse($horario->fecha)->format('d') }}</span>
            <span class="dow">{{ \Carbon\Carbon::parse($horario->fecha)->locale('es')->isoFormat('ddd') }}</span>
        </div>

        {{-- Póster --}}
        <div class="d-none d-md-flex align-items-center px-3" style="border-right:1px solid #2a2a2a">
            <img src="{{ $horario->pelicula->img ? asset('storage/'.$horario->pelicula->img) : asset('images/no-poster.svg') }}"
                 width="52" height="72"
                 class="rounded"
                 style="object-fit:cover"
                 alt="{{ $horario->pelicula->nom_pelicula }}"
                 onerror='this.onerror=null;this.src="{{ asset("images/no-poster.svg") }}"'>
        </div>

        {{-- Info principal --}}
        <div class="flex-grow-1 p-3">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-1">{{ $horario->pelicula->nom_pelicula }}</h6>
                    <div class="d-flex flex-wrap gap-2 text-secondary" style="font-size:.8rem">
                        {{-- Fecha en mobile --}}
                        <span class="d-sm-none">
                            <i class="bi bi-calendar-event me-1 text-danger"></i>
                            {{ \Carbon\Carbon::parse($horario->fecha)->locale('es')->isoFormat('D [de] MMM') }}
                        </span>
                        <span>
                            <i class="bi bi-clock me-1 text-danger"></i>
                            {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }}
                        </span>
                        <span>
                            <i class="bi bi-building me-1 text-danger"></i>
                            {{ $horario->sala->sucursal->nombre_suc ?? '' }} · Sala {{ $horario->sala->num_sala ?? '' }}
                        </span>
                        <span>
                            <i class="bi bi-clock-history me-1 text-secondary"></i>
                            {{ $horario->pelicula->duracion }} min
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <span class="badge bg-secondary">{{ $horario->tec_proyecc }}</span>
                        @php
                            $rangoColor = match($horario->pelicula->rango_edad) {
                                'TP'  => 'bg-success',
                                '+7'  => 'bg-info text-dark',
                                '+13' => 'bg-warning text-dark',
                                '+18' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $rangoColor }}">{{ $horario->pelicula->rango_edad }}</span>
                        @foreach($horario->pelicula->categorias->take(2) as $cat)
                            <span class="badge" style="background:#2a2a2a;color:#aaa;font-size:.65rem">{{ $cat->nom_categoria }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Acción --}}
                <div class="d-flex align-items-center">
                    @if(session('cliente'))
                        <a href="{{ route('reservas.create', ['horario' => $horario->id_horario]) }}"
                           class="btn btn-cine btn-sm px-3">
                            <i class="bi bi-ticket-detailed me-1"></i>Reservar
                        </a>
                    @else
                        <a href="{{ route('cliente.login') }}"
                           class="btn btn-outline-danger btn-sm px-3"
                           title="Inicia sesión para reservar">
                            <i class="bi bi-lock me-1"></i>Reservar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
