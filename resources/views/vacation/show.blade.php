@extends('template.base')

@section('content')

{{-- Modal para borrar comentarios --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa-solid fa-trash-can me-2"></i>¿Eliminar comentario?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                Esta acción no se puede deshacer. El comentario desaparecerá del historial de este destino.
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button form="form-delete" type="submit" class="btn btn-danger">Confirmar Eliminación</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Columna Izquierda: Imagen y Precio --}}
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden">
            <img class="img-fluid" 
                 src="{{ $vacation->foto ? $vacation->foto->getPath() : asset('assets/img/sin-foto.jpg') }}" 
                 alt="{{ $vacation->titulo }}"
                 style="width: 100%; height: 450px; object-fit: cover;">
            <div class="card-body bg-primary text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-uppercase small fw-bold">Precio desde</span>
                    <h2 class="mb-0">{{ number_format($vacation->precio, 2) }}€</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Columna Derecha: Información y Botón de Reserva --}}
    <div class="col-lg-7">
        <div class="ps-lg-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('main.index') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vacation.index') }}">Destinos</a></li>
                    <li class="breadcrumb-item active">{{ $vacation->titulo }}</li>
                </ol>
            </nav>
            
            <h1 class="display-4 fw-bold mb-3">{{ $vacation->titulo }}</h1>
            
            <div class="d-flex gap-3 mb-4">
                <span class="badge bg-light text-primary border px-3 py-2">
                    <i class="fa-solid fa-earth-americas me-2"></i>{{ $vacation->pais }}
                </span>
                <span class="badge bg-light text-info border px-3 py-2">
                    <i class="fa-solid fa-umbrella-beach me-2"></i>{{ $vacation->tipo->nombre }}
                </span>
            </div>

            <h4 class="fw-bold">Sobre este viaje</h4>
            <p class="lead text-muted mb-4">{{ $vacation->descripcion }}</p>

            {{-- BLOQUE DE RESERVA --}}
            <div class="card border-0 bg-light mb-4 shadow-sm" id="booking-section">
                <div class="card-body p-4">
                    @auth
                        @php $reserva = $vacation->reserva; @endphp

                        @if(Auth::user()->hasVerifiedEmail())
                            {{-- Caso: Usuario Verificado --}}
                            @if(!$reserva)
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-success">¡Disponible ahora!</h5>
                                        <p class="small text-muted mb-0">Confirma tu plaza en un solo clic.</p>
                                    </div>
                                    <form action="{{ route('reserva.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="idvacation" value="{{ $vacation->id }}">
                                        <button type="submit" class="btn btn-primary px-4 shadow-sm btn-lg">
                                            <i class="fa-solid fa-calendar-check me-2"></i>Reservar ahora
                                        </button>
                                    </form>
                                </div>
                            @else
                                @if($reserva->iduser == Auth::id())
                                    <div class="d-flex align-items-center text-success">
                                        <i class="fa-solid fa-circle-check fs-2 me-3"></i>
                                        <div>
                                            <h5 class="fw-bold mb-0">¡Tienes una reserva!</h5>
                                            <small>Este destino ya está en tu lista de próximos viajes.</small>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fa-solid fa-lock fs-2 me-3"></i>
                                        <div>
                                            <h5 class="fw-bold mb-0">No disponible</h5>
                                            <small>Ya reservado por otro viajero.</small>
                                        </div>
                                        <button class="btn btn-secondary ms-auto px-4" disabled>Agotado</button>
                                    </div>
                                @endif
                            @endif
                        @else
                            {{-- Caso: Usuario NO Verificado --}}
                            <div class="text-center py-2">
                                <i class="fa-solid fa-envelope-open-text text-warning fs-3 mb-2"></i>
                                <h6 class="fw-bold">Email no verificado</h6>
                                <p class="small text-muted mb-3">Para reservar este viaje, primero debes verificar tu cuenta de correo.</p>
                                <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-warning px-4 shadow-sm">Verificar mi email</a>
                            </div>
                        @endif
                    @else
                        {{-- Caso: No Logueado --}}
                        <div class="text-center py-2">
                            <p class="mb-2 small">Inicia sesión para poder gestionar tu reserva.</p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary px-4">Entrar a mi cuenta</a>
                        </div>
                    @endauth
                </div>
            </div>

            @auth
                @if(Auth::user()->isAdvanced())
                    <a href="{{ route('vacation.edit', $vacation->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Editar Anuncio
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>

<hr class="my-5">

{{-- SECCIÓN DE COMENTARIOS --}}
<div class="row justify-content-center pb-5">
    <div class="col-md-10">
        <div class="d-flex align-items-center mb-4">
            <h2 class="fw-bold mb-0">Experiencias de otros viajeros</h2>
            <span class="badge bg-primary ms-3">{{ $vacation->comentario->count() }}</span>
        </div>

        {{-- Lista de Comentarios --}}
        @forelse($vacation->comentario as $comentario)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-3 fs-5">"{{ $comentario->texto }}"</p>
                            <div class="text-muted small">
                                <i class="fa-solid fa-user-circle me-1"></i> 
                                {{ $comentario->user->name ?? 'Viajero Anónimo' }} • 
                                <i class="fa-solid fa-calendar-day ms-2 me-1"></i> {{ $comentario->created_at->format('d M, Y') }}
                            </div>
                        </div>

                        @auth
                            @php
                                $esAutor = ($comentario->iduser == Auth::id());
                                $idsEnSesion = session()->get('sentComentario')?->getIds() ?? [];
                                $enSesion = in_array($comentario->id, $idsEnSesion);
                            @endphp

                            <div class="d-flex gap-2">
                                @if($esAutor && $enSesion)
                                    <a href="{{ route('comentario.edit', $comentario->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                                    </a>
                                @endif

                                @if($esAutor || Auth::user()->isAdmin())
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                            data-href="{{ route('comentario.destroy', $comentario->id) }}" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fa-solid fa-trash-can me-1"></i> 
                                        {{ $esAutor ? 'Eliminar' : 'Moderar' }}
                                    </button>
                                @endif
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-light rounded-4">
                <p class="text-muted">Aún no hay opiniones sobre este destino.</p>
            </div>
        @endforelse

        {{-- FORMULARIO PARA COMENTAR (Restringido a Reservas) --}}
        <div class="mt-5 p-4 bg-white rounded-4 shadow-sm border text-center">
            <h4 class="mb-4 fw-bold">Cuéntanos tu experiencia</h4>

            @auth
                @php
                    // ¿El usuario logueado es el que ha reservado este viaje?
                    $usuarioHaReservado = ($vacation->reserva && $vacation->reserva->iduser == Auth::id());
                @endphp

                @if($usuarioHaReservado)
                    @include('comentario.create')
                @else
                    <div class="py-3">
                        <i class="fa-solid fa-comment-slash fs-2 text-muted mb-3"></i>
                        <h6 class="fw-bold">Opiniones exclusivas para viajeros</h6>
                        <p class="small text-muted mb-0">Solo los usuarios con una reserva confirmada en <strong>{{ $vacation->titulo }}</strong> pueden publicar comentarios.</p>
                    </div>
                @endif
            @else
                <p class="small text-muted">Inicia sesión y reserva para poder comentar.</p>
                <a href="{{ route('login') }}" class="btn btn-sm btn-primary px-4">Login</a>
            @endauth
        </div>
    </div>
</div>

{{-- Formulario oculto para el DELETE --}}
<form id="form-delete" action="" method="post" class="d-none">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteModal');
        const formDelete = document.getElementById('form-delete');

        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            formDelete.setAttribute('action', button.getAttribute('data-href'));
        });
    });
</script>
@endsection