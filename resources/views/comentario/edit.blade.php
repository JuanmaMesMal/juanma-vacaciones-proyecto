@extends('template.base')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('main.index') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vacation.show', $comentario->idvacation) }}">Destino</a></li>
                    <li class="breadcrumb-item active">Editar Comentario</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-header bg-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 me-3">
                            <i class="fa-solid fa-pen-nib text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold">Actualizar tu experiencia</h3>
                            <p class="mb-0 small opacity-75">Publicado originalmente el {{ $comentario->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-lg-5">
                    <form action="{{ route('comentario.update', $comentario->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="alert alert-danger border-0 shadow-sm mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="texto" class="form-label fw-bold text-dark">Tu opinión sobre el viaje:</label>
                            <textarea class="form-control form-control-lg border-2" 
                                      name="texto" 
                                      id="texto" 
                                      rows="6" 
                                      placeholder="¿Qué te pareció este destino?..." 
                                      required>{{ old('texto', $comentario->texto) }}</textarea>
                            <div class="form-text mt-2">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Recuerda que tus comentarios ayudan a otros viajeros a elegir su próximo destino.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('vacation.show', $comentario->idvacation) }}" class="btn btn-link text-muted text-decoration-none">
                                <i class="fa-solid fa-arrow-left me-2"></i>Cancelar y volver
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 p-3 bg-light rounded-3 border d-flex align-items-center">
                <i class="fa-solid fa-plane-departure text-primary me-3 fs-4"></i>
                <span class="text-muted">Estás editando un comentario para: <strong>{{ $comentario->vacation->titulo }}</strong></span>
            </div>
        </div>
    </div>
</div>
@endsection