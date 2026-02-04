@extends('template.base')

@section('content')

@yield('anytitle')
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Borrando vacation</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Estas a punto de eliminar el perfil de este vacation, ¿estás seguro de que lo quieres hacer?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-bs-dismiss="modal">Cerrar</button>
        <button form="form-delete" type="submit" class="btn btn-danger">Eliminar Perfil</button>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm mb-4 p-3 bg-white rounded-4">
    <form action="{{ $urlDestino }}" method="get" class="row g-3 align-items-end">
        
        {{-- Buscador General --}}
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Búsqueda</label>
            <input type="search" name="q" class="form-control" placeholder="¿A dónde vamos?" value="{{ $q }}">
        </div>

        {{-- Filtro Tipo --}}
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted">Categoría</label>
            <select name="idtipo" class="form-select">
                <option value="">Todas</option>
                @foreach($tipos as $id => $nombre)
                    <option value="{{ $id }}" {{ $id == $idtipo ? 'selected' : '' }}>{{ $nombre }}</option>
                @endforeach
            </select>
        </div>

        {{-- Selección de Campo para Ordenar --}}
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted">Ordenar por</label>
            <select name="campo" class="form-select">
                <option value="titulo" {{ $campo == 'titulo' ? 'selected' : '' }}>Nombre</option>
                <option value="precio" {{ $campo == 'precio' ? 'selected' : '' }}>Precio</option>
                @auth
                  @if(Auth::user()->isAdvanced())
                    <option value="id" {{ $campo == 'id' ? 'selected' : '' }}>ID</option>
                  @endif
                @endauth
            </select>
        </div>

        {{-- Dirección de Orden --}}
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted">Dirección</label>
            <select name="orden" class="form-select">
                <option value="asc" {{ $orden == 'asc' ? 'selected' : '' }}>Ascendente</option>
                <option value="desc" {{ $orden == 'desc' ? 'selected' : '' }}>Descendente</option>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Filtrar
            </button>
            <a href="{{ route('main.index') }}" class="btn btn-outline-secondary" title="Limpiar">
                <i class="fa-solid fa-erasero">Borrar</i>
            </a>
        </div>
    </form>
</div>

<div class="container mt-4">
  <div class="row">
    @forelse($vacations as $vacation)
        <div class="col-md-4 mb-4">
          <div class="card h-100">
              <span class="travel-badge"><i class="fa-solid fa-location-dot me-1"></i> {{ $vacation->pais }}</span>
              <img class="card-img-top" src="{{ $vacation->foto ? $vacation->foto->getPath() : asset('assets/img/sin-foto.jpg') }}">
              
              <div class="card-body p-4">
                  <h5 class="card-title">{{ $vacation->titulo }}</h5>
                  <p class="text-muted small mb-3">{{ Str::limit($vacation->descripcion, 80) }}</p>
                  <div class="d-flex justify-content-between align-items-center">
                      <div class="travel-price">{{ number_format($vacation->precio, 0) }}€</div>
                      <a class="badge bg-light text-primary" style="text-decoration: none;" href="{{ route('vacation.tipo', $vacation->idtipo) }}">{{ $vacation->tipo->nombre }}</a>
                  </div>
              </div>
              <div class="card-footer bg-white border-0 pb-4 px-4 d-flex gap-2">
                  <a href="{{ route('vacation.show', $vacation->id) }}" class="btn btn-sm btn-outline-primary w-100">Detalles</a>
                  @auth
                    @if(Auth::user()->isAdvanced())
                      <a href="{{ route('vacation.edit', $vacation->id) }}" class="btn btn-sm btn-outline-secondary" style="display: flex; gap:.4rem; align-items: center;">
                        <i class="fa-solid fa-pen"></i>Editar
                      </a>
                    @endif
                  @endauth
              </div>
          </div>
      </div>
    @empty
      <p class="text-center w-100">No hay vacaciones registradas.</p>
    @endforelse
  </div>
</div>

    <form id="form-delete" action="" method="post">
        @csrf
        @method('DELETE')
    </form>
</div>

<div class="d-flex justify-content-center mt-5">
    {{ $vacations->onEachSide(2)->links() }}
</div>

@endsection

@section('scripts')
  <script>
      // Espera a que el documento HTML esté completamente cargado
      document.addEventListener('DOMContentLoaded', function () {
          // Selecciona todos los botones que abren el modal de borrado
          const deleteButtons = document.querySelectorAll('[data-bs-target="#deleteModal"]');
          // Selecciona el formulario que se usará para enviar la petición DELETE
          const formDelete = document.getElementById('form-delete');

          // Recorre cada botón de "delete"
          deleteButtons.forEach(button => {
              // Añade un listener para cuando se haga clic en él
              button.addEventListener('click', function () {
                  // Cuando se hace clic, obtiene la URL guardada en el atributo 'data-href' del botón específico     
                  const action = this.getAttribute('data-href');
                  // Asigna esa URL específica al atributo 'action' del formulario de borrado
                  formDelete.setAttribute('action', action);
              });
          });
      });
  </script>
@endsection
