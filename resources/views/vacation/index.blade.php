@extends('template.base')

@section('content')

{{-- 1. MODAL DE CONFIRMACIÓN (Misma lógica, diseño limpio) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-5 text-center">
                <div class="mb-4 text-danger opacity-75">
                    <i class="fa-solid fa-trash-can fa-4x"></i>
                </div>
                <h3 class="fw-bold mb-2">¿Eliminar destino?</h3>
                <p class="text-muted mb-4">
                    Estás a punto de borrar este viaje permanentemente.<br>
                    <span class="small text-danger">Esta acción no se puede deshacer.</span>
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button form="form-delete" type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                        Sí, eliminarlo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    
    {{-- 2. CABECERA DEL DASHBOARD --}}
    <div class="dashboard-header">
        <div>
            <h1 class="fw-bold text-dark mb-1">Gestión de Destinos</h1>
            <p class="text-muted mb-0">Administra tus viajes, precios y categorías.</p>
        </div>
        
        @auth
            @if(Auth::user()->isAdvanced())
            <a href="{{ route('vacation.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-plus bg-white text-primary rounded-circle p-1" style="font-size: 0.8rem;"></i>
                <span>Nuevo Destino</span>
            </a>
            @endif
        @endauth
    </div>

    {{-- 3. LISTA DE GESTIÓN (Reemplazo de la Tabla) --}}
    <div class="admin-list-container">
        
        @forelse($vacations as $vacation)
            <div class="admin-item">
                {{-- ID --}}
                <div class="fw-bold text-muted small pe-2">#{{ $vacation->id }}</div>
                
                {{-- Imagen --}}
                <img src="{{ $vacation->foto ? $vacation->foto->getPath() : asset('assets/img/sin-foto.jpg') }}" 
                     alt="{{ $vacation->titulo }}" 
                     class="admin-thumb">

                {{-- Info Principal --}}
                <div class="info-col main">
                    <h5 class="fw-bold text-dark mb-1">{{ $vacation->titulo }}</h5>
                    <div class="text-muted small">
                        <i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $vacation->pais }}
                    </div>
                </div>

                {{-- Info Meta (Precio/Cat) --}}
                <div class="info-col meta">
                    <span class="fw-bold text-primary fs-5">{{ number_format($vacation->precio, 2) }}€</span>
                    <a href="{{ route('vacation.tipo', $vacation->idtipo) }}" class="badge bg-light text-secondary border text-decoration-none rounded-pill px-3 py-2">
                        {{ $vacation->tipo->nombre }}
                    </a>
                </div>

                {{-- Acciones --}}
                <div class="info-col actions">
                    <div class="d-flex gap-2">
                        {{-- Botón Ver --}}
                        <a href="{{ route('vacation.show', $vacation->id) }}" class="action-btn" title="Ver en web">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        @auth
                            @if(Auth::user()->isAdvanced())
                                {{-- Botón Editar --}}
                                <a href="{{ route('vacation.edit', $vacation->id) }}" class="action-btn text-warning" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                {{-- Botón Eliminar (Trigger Modal) --}}
                                <button type="button" 
                                        class="action-btn delete text-danger" 
                                        data-href="{{ route('vacation.destroy', $vacation->id) }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal" 
                                        title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            {{-- Estado Vacío --}}
            <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                <div class="mb-3 text-muted opacity-25">
                    <i class="fa-solid fa-passport fa-4x"></i>
                </div>
                <h4 class="text-muted">No hay destinos registrados</h4>
                <p class="small text-muted mb-0">Empieza creando uno nuevo con el botón superior.</p>
            </div>
        @endforelse

    </div>

    {{-- Paginación (Si la usas, se añade aquí, igual que antes) --}}
    @if(method_exists($vacations, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $vacations->links() }}
        </div>
    @endif

</div>

{{-- Formulario Oculto para Delete --}}
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

        // Lógica del Modal (Bootstrap 5)
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const action = button.getAttribute('data-href');
            formDelete.setAttribute('action', action);
        });
    });
</script>
@endsection