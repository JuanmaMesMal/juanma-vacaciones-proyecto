@extends('template.base')

@section('content')
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-primary mb-0">Editar Destino</h1>
                    <p class="text-muted">Modificando: <strong>{{ $vacation->titulo }}</strong></p>
                </div>
                <a href="{{ route('vacation.show', $vacation->id) }}" class="btn btn-outline-primary shadow-sm">
                    <i class="fa-solid fa-eye me-2"></i>Ver Actual
                </a>
            </div>

            <form action="{{ route('vacation.update', $vacation->id) }}" method="POST" enctype="multipart/form-data" class="row g-4">
                @csrf
                @method('put')

                @if ($errors->any())
                    <div class="col-12">
                        <div class="alert alert-danger border-0 shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fa-solid fa-circle-exclamation me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="col-md-7">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <h4 class="mb-4 fw-bold text-dark"><i class="fa-solid fa-pen-nib text-primary me-2"></i>Contenido del Anuncio</h4>
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label fw-semibold">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" 
                                   value="{{ old('titulo', $vacation->titulo) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-semibold">Descripción Detallada</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="8" required>{{ old('descripcion', $vacation->descripcion) }}</textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="pais" class="form-label fw-semibold">País</label>
                                <input type="text" class="form-control" id="pais" name="pais" value="{{ old('pais', $vacation->pais) }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="idtipo" class="form-label fw-semibold">Categoría</label>
                                <select required name="idtipo" id="idtipo" class="form-select">
                                    @foreach($tipos as $idTipo => $nombreTipo)
                                        <option value="{{ $idTipo }}" @selected($idTipo == old("idtipo", $vacation->idtipo))>
                                            {{ $nombreTipo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="d-flex flex-column gap-4">
                        
                        <div class="card border-0 shadow-sm p-4 bg-primary text-white">
                            <h4 class="mb-3 fw-bold"><i class="fa-solid fa-coins me-2"></i>Tarifas</h4>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-0 bg-white text-primary fw-bold">€</span>
                                <input type="number" class="form-control border-0" id="precio" name="precio" 
                                       step=".1" value="{{ old('precio', $vacation->precio) }}" required>
                            </div>
                            <small class="mt-2 opacity-75">Este precio se mostrará como "desde" en el catálogo.</small>
                        </div>

                        <div class="card border-0 shadow-sm p-4">
                            <h4 class="mb-3 fw-bold text-dark"><i class="fa-solid fa-camera text-primary me-2"></i>Imagen de Portada</h4>
                            
                            <div id="drop-zone" class="p-3 text-center border-dashed mb-3">
                                <p class="small text-muted mb-3" id="drop-text">Arrastra para actualizar la imagen</p>
                                <img id="preview" 
                                    src="{{ $vacation->foto ? $vacation->foto->getPath() : asset('assets/img/sin-foto.jpg') }}"
                                    alt="Vista previa"
                                    class="img-fluid rounded shadow-sm mb-3"
                                    style="max-height: 150px; object-fit: cover;">
                                <br>
                                <button type="button" class="btn btn-sm btn-light border" onclick="document.getElementById('image').click()">
                                    Cambiar Imagen
                                </button>
                            </div>

                            <input type="file" id="image" name="image" accept="image/*" class="d-none">

                            @if($vacation->foto)
                                <div class="form-check form-switch p-3 bg-light rounded border border-danger-subtle">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="delete_image" id="delete_image">
                                    <label class="form-check-label text-danger fw-bold" for="delete_image">
                                        <i class="fa-solid fa-trash-can me-1"></i> Eliminar foto actual
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-5 text-center">
                    <div class="bg-white p-4 rounded-4 shadow-sm border">
                        <button type="submit" class="btn btn-primary btn-lg px-5 me-2">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios
                        </button>
                        <a href="{{ route('main.index') }}" class="btn btn-outline-secondary btn-lg px-4">Descartar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mantenemos tu lógica de JavaScript pero aplicada a los nuevos IDs y clases
    document.addEventListener('DOMContentLoaded', function () {
        const dropZone = document.getElementById('drop-zone');
        const inputFile = document.getElementById('image');
        const preview = document.getElementById('preview');
        const dropText = document.getElementById('drop-text');

        dropZone.addEventListener('click', () => inputFile.click());

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
            inputFile.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        });

        function handleFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                dropText.textContent = "Nueva imagen lista";
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection