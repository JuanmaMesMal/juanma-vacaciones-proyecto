@extends('template.base')

@section('content')
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold text-primary">Publicar Nueva Aventura</h1>
                <p class="text-muted">Completa los detalles para inspirar a tu próximo viajero.</p>
            </div>

            <form action="{{ route('vacation.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                @csrf

                {{-- Errores de Validación --}}
                @if ($errors->any())
                    <div class="col-12">
                        <div class="alert alert-danger shadow-sm border-0">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="col-md-7">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <h4 class="mb-4 fw-bold"><i class="fa-solid fa-circle-info text-primary me-2"></i>Información del Destino</h4>
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label fw-semibold">Título del Anuncio</label>
                            <input type="text" class="form-control form-control-lg" id="titulo" name="titulo" 
                                   placeholder="Ej: Escapada Mágica a las Islas Griegas" value="{{ old('titulo') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-semibold">Descripción del Viaje</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="6"
                                      placeholder="Describe la experiencia, los lugares que visitarán..." required>{{ old('descripcion') }}</textarea>
                            <div class="form-text">Mínimo 20 caracteres para una descripción atractiva.</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="pais" class="form-label fw-semibold">País de Destino</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fa-solid fa-earth-americas text-muted"></i></span>
                                    <input type="text" class="form-control" id="pais" name="pais" placeholder="Ej: Italia" value="{{ old('pais') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="idtipo" class="form-label fw-semibold">Estilo de Viaje</label>
                                <select required name="idtipo" id="idtipo" class="form-select">
                                    <option value="" disabled @selected(old('idtipo') == null)>-- Elegir estilo --</option>
                                    @foreach($tipos as $idTipo => $nombreTipo)
                                        <option value="{{ $idTipo }}" @selected(old('idtipo') == $idTipo)>{{ $nombreTipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="d-flex flex-column gap-4 h-100">
                        
                        <div class="card border-0 shadow-sm p-4 bg-primary text-white">
                            <h4 class="mb-3 fw-bold"><i class="fa-solid fa-tag me-2"></i>Inversión</h4>
                            <label for="precio" class="form-label small">Precio por persona (€)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-0">€</span>
                                <input type="number" class="form-control border-0" id="precio" name="precio" 
                                       placeholder="0.00" min="0" step=".1" value="{{ old('precio') }}" required>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm p-4 flex-grow-1">
                            <h4 class="mb-3 fw-bold"><i class="fa-solid fa-image text-primary me-2"></i>Portada</h4>
                            
                            <div id="drop-zone" class="d-flex flex-column align-items-center justify-content-center p-3 text-center border-dashed">
                                <p class="small text-muted mb-3" id="drop-text">Suelta la mejor foto aquí</p>

                                <img id="preview" 
                                    src="{{ asset('assets/img/sin-foto.jpg') }}"
                                    alt="Vista previa"
                                    class="img-fluid rounded-3 mb-3 shadow-sm"
                                    style="max-height: 180px; object-fit: cover;">
                                
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('image').click()">
                                    Seleccionar Archivo
                                </button>
                            </div>

                            <input type="file" id="image" name="image" accept="image/*" class="d-none">
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                        <i class="fa-solid fa-paper-plane me-2"></i>Publicar Destino
                    </button>
                    <a href="{{ route('vacation.index') }}" class="btn btn-link text-muted ms-3 text-decoration-none">Cancelar y volver</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('drop-zone');
        const inputFile = document.getElementById('image');
        const preview = document.getElementById('preview');
        const dropText = document.getElementById('drop-text');

        // Reutilizamos tu lógica pero con mejoras visuales en los textos
        inputFile.addEventListener('change', (event) => {
            handleFile(event.target.files[0]);
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
            dropZone.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); });
        });

        ['dragenter', 'dragover'].forEach(evt => {
            dropZone.addEventListener(evt, () => dropZone.classList.add('bg-light', 'border-primary'));
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropZone.addEventListener(evt, () => dropZone.classList.remove('bg-light', 'border-primary'));
        });

        dropZone.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            inputFile.files = e.dataTransfer.files;
            handleFile(file);
        });

        function handleFile(file) {
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                dropText.textContent = "¡Imagen lista!";
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection