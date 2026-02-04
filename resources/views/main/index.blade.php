@extends('template.base')

@section('content')

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <i class="fa-solid fa-triangle-exclamation text-warning fa-3x mb-3"></i>
        <p class="mb-0">Estás a punto de eliminar este destino vacacional.<br>Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
        <button form="form-delete" type="submit" class="btn btn-danger px-4">Sí, eliminar</button>
      </div>
    </div>
  </div>
</div>

<div class="hero-header">
    <div class="hero-title">
        @yield('anytitle') 
        <h1>Encuentra tu paraíso</h1>
        <p class="lead opacity-75">Explora los mejores destinos al mejor precio</p>
    </div>
</div>

<div class="container position-relative">
    
    <div class="search-floater">
        <form action="{{ $urlDestino }}" method="get" class="search-grid">
            
            {{-- Input Búsqueda --}}
            <div class="custom-input-group">
                <label><i class="fa-solid fa-magnifying-glass me-1"></i> Destino</label>
                <input type="search" name="q" class="custom-control" placeholder="¿A dónde vamos?" value="{{ $q }}">
            </div>

            {{-- Select Categoría --}}
            <div class="custom-input-group">
                <label><i class="fa-regular fa-compass me-1"></i> Tipo</label>
                <select name="idtipo" class="custom-control">
                    <option value="">Todas</option>
                    @foreach($tipos as $id => $nombre)
                        <option value="{{ $id }}" {{ $id == $idtipo ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Select Ordenar --}}
            <div class="custom-input-group">
                <label><i class="fa-solid fa-sort me-1"></i> Ordenar</label>
                <select name="campo" class="custom-control">
                    <option value="titulo" {{ $campo == 'titulo' ? 'selected' : '' }}>Nombre</option>
                    <option value="precio" {{ $campo == 'precio' ? 'selected' : '' }}>Precio</option>
                    @auth
                      @if(Auth::user()->isAdvanced())
                        <option value="id" {{ $campo == 'id' ? 'selected' : '' }}>ID</option>
                      @endif
                    @endauth
                </select>
            </div>

            {{-- Select Dirección --}}
            <div class="custom-input-group">
                <label><i class="fa-solid fa-arrow-down-wide-short me-1"></i> Dirección</label>
                <select name="orden" class="custom-control">
                    <option value="asc" {{ $orden == 'asc' ? 'selected' : '' }}>Asc</option>
                    <option value="desc" {{ $orden == 'desc' ? 'selected' : '' }}>Desc</option>
                </select>
            </div>

            {{-- Botones de Acción --}}
            <div class="d-flex gap-2 pb-1">
                <button type="submit" class="btn-travel-primary w-100">
                    Buscar
                </button>
                <a href="{{ route('main.index') }}" class="btn-travel-reset" title="Limpiar filtros">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{--  GRID DE RESULTADOS  --}}
    <div class="row g-4 mb-5">
        @forelse($vacations as $vacation)
            <div class="col-md-6 col-lg-4">
                <article class="travel-card h-100 d-flex flex-column">
                    
                    {{-- Imagen con badges --}}
                    <div class="card-img-wrapper">
                        <span class="location-tag">
                            <i class="fa-solid fa-earth-americas me-1"></i> {{ $vacation->pais }}
                        </span>
                        
                        <img src="{{ $vacation->foto ? $vacation->foto->getPath() : asset('assets/img/sin-foto.jpg') }}" alt="{{ $vacation->titulo }}">
                        
                        <div class="price-float">
                            {{ number_format($vacation->precio, 0) }}€
                        </div>
                    </div>
                    
                    {{-- Cuerpo de la tarjeta --}}
                    <div class="card-content flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0 text-dark">{{ $vacation->titulo }}</h5>
                            <a href="{{ route('vacation.tipo', $vacation->idtipo) }}" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none rounded-pill px-3">
                                {{ $vacation->tipo->nombre }}
                            </a>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            {{ Str::limit($vacation->descripcion, 90) }}
                        </p>
                    </div>

                    {{-- Footer de acciones --}}
                    <div class="card-actions">
                        <a href="{{ route('vacation.show', $vacation->id) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 fw-bold">
                            Ver Detalles
                        </a>
                        
                        @auth
                            @if(Auth::user()->isAdvanced())
                                <a href="{{ route('vacation.edit', $vacation->id) }}" class="btn btn-light btn-sm rounded-circle text-secondary" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-light btn-sm rounded-circle text-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal" 
                                        data-href="{{ route('vacation.destroy', $vacation->id) }}" {{-- Asumo ruta destroy estándar --}}
                                        title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif
                        @endauth
                    </div>

                </article>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted opacity-50 mb-3">
                    <i class="fa-solid fa-plane-slash fa-4x"></i>
                </div>
                <h3>No hemos encontrado destinos</h3>
                <p class="text-muted">Intenta ajustar tus filtros de búsqueda.</p>
            </div>
        @endforelse
    </div>

    {{-- 4. PAGINACIÓN --}}
    <div class="d-flex justify-content-center pb-5">
        {{ $vacations->onEachSide(2)->links() }}
    </div>

</div>

{{-- Formulario oculto para el delete --}}
<form id="form-delete" action="" method="post">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
  <script>
      document.addEventListener('DOMContentLoaded', function () {
          const deleteButtons = document.querySelectorAll('[data-bs-target="#deleteModal"]');
          const formDelete = document.getElementById('form-delete');

          deleteButtons.forEach(button => {
              button.addEventListener('click', function () {
                  // OJO: Asegúrate de que tus botones de borrar tengan el atributo data-href con la ruta
                  const action = this.getAttribute('data-href');
                  formDelete.setAttribute('action', action);
              });
          });
      });
  </script>
@endsection