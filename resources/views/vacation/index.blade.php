@extends('template.base')

@section('content')

{{-- Modal de Confirmación --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Atención</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="fs-5 mb-0">¿Estás seguro de que deseas eliminar este destino?</p>
                <small class="text-muted">Esta acción borrará todas las fotos y comentarios asociados.</small>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                <button form="form-delete" type="submit" class="btn btn-danger px-4 shadow-sm">Eliminar Definitivamente</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid py-4">
    {{-- Cabecera con estadísticas rápidas --}}
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="display-6 fw-bold text-primary mb-0">Gestión de Destinos</h1>
            <p class="text-muted">Tienes un total de <strong>{{ count($vacations) }}</strong> paquetes vacacionales activos.</p>
        </div>
        @auth
            @if(Auth::user()->isAdvanced())
            <a href="{{ route('vacation.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fa-solid fa-plus me-2"></i>Nuevo Destino
            </a>
            @endif
        @endauth
    </div>

    {{-- Tabla Estilizada --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase">
                    <tr>
                        <th class="ps-4 py-3 text-muted small">#</th>
                        <th class="py-3 text-muted small">Destino</th>
                        <th class="py-3 text-muted small">Categoría</th>
                        <th class="py-3 text-muted small">Precio</th>
                        <th class="py-3 text-center text-muted small">Acciones</th>
                    </tr>
                </thead>
                <tbody> 
                    @forelse($vacations as $vacation)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">{{ $vacation->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $vacation->foto ? $vacation->foto->getPath() : asset('assets/img/sin-foto.jpg') }}" 
                                     alt="{{ $vacation->titulo }}"
                                     class="rounded-3 shadow-sm me-3"
                                     style="height: 55px; width: 55px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $vacation->titulo }}</div>
                                    <div class="small text-muted"><i class="fa-solid fa-location-dot me-1"></i>{{ $vacation->pais }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-info-subtle text-info px-3 border border-info-subtle">
                                <i class="fa-solid fa-tag me-1 small"></i><a class="text-primary" style="text-decoration: none;" href="{{ route('vacation.tipo', $vacation->idtipo) }}">{{ $vacation->tipo->nombre }}</a>
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-primary fs-5">{{ number_format($vacation->precio, 2) }}€</div>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group shadow-sm rounded">
                                <a href="{{ route('vacation.show', $vacation->id) }}" class="btn btn-white btn-sm px-3 border" title="Ver detalle">
                                    <i class="fa-solid fa-eye text-success"></i> Ver
                                </a>
                                @auth
                                    @if(Auth::user()->isAdvanced())
                                    <a href="{{ route('vacation.edit', $vacation->id) }}" class="btn btn-white btn-sm px-3 border" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-warning"></i> Editar
                                    </a>
                                    <button type="button" 
                                            class="btn btn-white btn-sm px-3 border" 
                                            data-href="{{ route('vacation.destroy', $vacation->id) }}" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal" 
                                            title="Eliminar">
                                        <i class="fa-solid fa-trash-can text-danger"></i> Eliminar
                                    </button>
                                    @endif
                                @endauth
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fa-solid fa-plane-slash display-4 mb-3"></i>
                                <p class="fs-5">No se encontraron destinos registrados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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

        // Escuchamos el evento de apertura del modal de Bootstrap
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // El botón que disparó el modal
            const button = event.relatedTarget;
            // Extraemos la info de los atributos data-*
            const action = button.getAttribute('data-href');
            // Actualizamos el action del form
            formDelete.setAttribute('action', action);
        });
    });
</script>
@endsection