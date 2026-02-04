@extends('template.base')

@section('content')

{{-- MODAL DE BORRAR COMENTARIO (Misma lógica, diseño limpio) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-5 text-center">
                <div class="mb-3 text-danger opacity-75">
                    <i class="fa-solid fa-comment-slash fa-3x"></i>
                </div>
                <h4 class="fw-bold">¿Borrar opinión?</h4>
                <p class="text-muted small">Esta acción eliminará el comentario permanentemente.</p>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button form="form-delete" type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, borrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">

    {{-- 1. CABECERA: Título y Ubicación --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="mb-2">{{ $vacation->titulo }}</h1>
                <div class="d-flex align-items-center gap-3 text-muted">
                    <span><i class="fa-solid fa-location-dot text-primary me-1"></i> {{ $vacation->pais }}</span>
                    <span>•</span>
                    <span><i class="fa-solid fa-tag me-1"></i> {{ $vacation->tipo->nombre }}</span>
                    
                    @auth
                        @if(Auth::user()->isAdvanced())
                            <a href="{{ route('vacation.edit', $vacation->id) }}" class="text-secondary ms-2 small text-decoration-underline">
                                <i class="fa-solid fa-pen"></i> Editar
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- 2. IMAGEN GRANDE (Estilo Galería) --}}
    <div class="position-relative">
        <img src="{{ $vacation->foto ? $vacation->foto->getPath() : asset('assets/img/sin-foto.jpg') }}" 
             alt="{{ $vacation->titulo }}" 
             class="detail-gallery-img">
    </div>

    {{-- 3. CONTENIDO PRINCIPAL (Grid 2 Columnas) --}}
    <div class="row g-5">
        
        {{-- COLUMNA IZQUIERDA: Descripción y Comentarios --}}
        <div class="col-lg-8">
            
            <div class="mb-5">
                <h3 class="fw-bold mb-3">Sobre este destino</h3>
                <p class="lead text-secondary" style="line-height: 1.8;">
                    {{ $vacation->descripcion }}
                </p>
            </div>

            <hr class="my-5 opacity-25">

            {{-- SECCIÓN COMENTARIOS --}}
            <div class="d-flex align-items-center mb-4">
                <h3 class="fw-bold mb-0">Opiniones de viajeros</h3>
                <span class="badge bg-dark rounded-pill ms-3 px-3">{{ $vacation->comentario->count() }}</span>
            </div>

            {{-- Lista de Comentarios --}}
            <div class="d-flex flex-column gap-3 mb-5">
                @forelse($vacation->comentario as $comentario)
                    <div class="comment-card">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-3">
                                {{-- Avatar Simulado con Inicial --}}
                                <div class="avatar-circle">
                                    {{ substr($comentario->user->name ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $comentario->user->name ?? 'Anónimo' }}</h6>
                                    <small class="text-muted">{{ $comentario->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            {{-- Botones de Acción (Dueño/Admin) --}}
                            @auth
                                @php
                                    $esAutor = ($comentario->iduser == Auth::id());
                                    $idsEnSesion = session()->get('sentComentario')?->getIds() ?? [];
                                    $enSesion = in_array($comentario->id, $idsEnSesion);
                                @endphp

                                @if($esAutor || Auth::user()->isAdmin())
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <ul class="dropdown-menu border-0 shadow">
                                            @if($esAutor && $enSesion)
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('comentario.edit', $comentario->id) }}">
                                                        <i class="fa-solid fa-pen small me-2"></i> Editar
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <button class="dropdown-item text-danger" 
                                                        type="button" 
                                                        data-href="{{ route('comentario.destroy', $comentario->id) }}" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                    <i class="fa-solid fa-trash small me-2"></i> Eliminar
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            @endauth
                        </div>
                        
                        <p class="mb-0 text-secondary ps-5 ms-2">{{ $comentario->texto }}</p>
                    </div>
                @empty
                    <div class="p-4 rounded-3 bg-light text-center">
                        <i class="fa-regular fa-comments fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Aún no hay reseñas. ¡Sé el primero en contar tu experiencia!</p>
                    </div>
                @endforelse
            </div>

            {{-- FORMULARIO COMENTAR (Logica Original) --}}
            @auth
                @php
                    $usuarioHaReservado = ($vacation->reserva && $vacation->reserva->iduser == Auth::id());
                @endphp

                @if($usuarioHaReservado)
                    <div class="bg-white border rounded-4 p-4 shadow-sm">
                        <h5 class="fw-bold mb-3">Deja tu reseña</h5>
                        @include('comentario.create')
                    </div>
                @endif
            @endauth

        </div>

        {{-- COLUMNA DERECHA: Tarjeta de Reserva (Sticky) --}}
        <div class="col-lg-4">
            <div class="booking-sidebar">
                
                {{-- Precio --}}
                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
                    <span class="text-muted fw-bold">Precio total</span>
                    <div>
                        <span class="booking-price">{{ number_format($vacation->precio, 0) }}€</span>
                    </div>
                </div>

                {{-- LÓGICA DE RESERVA (INTACTA) --}}
                @auth
                    @php $reserva = $vacation->reserva; @endphp

                    @if(Auth::user()->hasVerifiedEmail())
                        
                        @if(!$reserva)
                            {{-- DISPONIBLE --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center text-success mb-3 bg-success bg-opacity-10 p-2 rounded">
                                    <i class="fa-solid fa-check-circle me-2"></i>
                                    <small class="fw-bold">Destino disponible</small>
                                </div>
                                
                                <form action="{{ route('reserva.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="idvacation" value="{{ $vacation->id }}">
                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm btn-lg">
                                        Reservar ahora
                                    </button>
                                </form>
                                <p class="text-center text-muted small mt-2">No se te cobrará nada todavía.</p>
                            </div>

                        @else
                            {{-- ESTADOS DE RESERVA --}}
                            @if($reserva->iduser == Auth::id())
                                <div class="text-center py-4 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25">
                                    <i class="fa-solid fa-circle-check text-success fa-3x mb-3"></i>
                                    <h5 class="fw-bold text-success">¡Todo listo!</h5>
                                    <p class="text-muted small mb-0">Ya tienes tu plaza reservada.</p>
                                </div>
                            @else
                                <div class="text-center py-4 bg-secondary bg-opacity-10 rounded-3">
                                    <i class="fa-solid fa-lock text-secondary fa-3x mb-3"></i>
                                    <h5 class="fw-bold text-secondary">Agotado</h5>
                                    <p class="text-muted small mb-0">Este destino ya no está disponible.</p>
                                </div>
                            @endif
                        @endif

                    @else
                        {{-- NO VERIFICADO --}}
                        <div class="alert alert-warning border-0 shadow-sm text-center">
                            <i class="fa-solid fa-envelope mb-2"></i><br>
                            <strong>Verifica tu email</strong>
                            <p class="small mb-2">Necesitas verificar tu cuenta para reservar.</p>
                            <a href="{{ route('verification.notice') }}" class="btn btn-warning btn-sm w-100 fw-bold">Enviar correo</a>
                        </div>
                    @endif

                @else
                    {{-- NO LOGUEADO --}}
                    <div class="text-center p-3 bg-light rounded-3">
                        <p class="small text-muted mb-3">Inicia sesión para reservar este viaje.</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-dark w-100 rounded-pill fw-bold">Entrar</a>
                    </div>
                @endauth

                {{-- Info Extra --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span><i class="fa-solid fa-shield-halved me-2"></i>Seguro de viaje</span>
                        <span>Incluido</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span><i class="fa-solid fa-suitcase me-2"></i>Equipaje</span>
                        <span>20kg</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Formulario oculto para eliminar --}}
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

        // Lógica para pasar la ruta al formulario del modal
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const action = button.getAttribute('data-href');
            formDelete.setAttribute('action', action);
        });
    });
</script>
@endsection