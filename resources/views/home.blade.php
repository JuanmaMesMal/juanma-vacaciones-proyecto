@extends('template.base')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <div class="card border-0 shadow-sm rounded-4 mb-5">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-user fs-1"></i>
                            </div>
                            <div class="ms-4">
                                <h2 class="fw-bold mb-0">¡Hola, {{ Auth::user()->name }}!</h2>
                                <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                                <span class="badge bg-light text-primary border mt-2">
                                    <i class="fa-solid fa-id-badge me-1"></i> 
                                    {{ Auth::user()->isAdvanced() ? 'Usuario Advanced' : 'Usuario Estándar' }}
                                </span>
                            </div>
                        </div>
                        <hr class="text-muted opacity-25">
                        <div class="row text-center mt-4">
                            <div class="col-md-4">
                                <h4 class="fw-bold text-primary mb-0">{{ Auth::user()->reservas ? Auth::user()->reservas->count() : 0 }}</h4>
                                <small class="text-muted text-uppercase fw-bold">Reservas Totales</small>
                            </div>
                            <div class="col-md-4">

                            </div>
                            <div class="col-md-4">
                                <h4 class="fw-bold text-primary mb-0">{{ Auth::user()->created_at->format('M Y') }}</h4>
                                <small class="text-muted text-uppercase fw-bold">Miembro desde</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: MIS RESERVAS --}}
                <div class="d-flex align-items-center mb-4">
                    <h3 class="fw-bold mb-0"><i class="fa-solid fa-suitcase-rolling text-primary me-2"></i>Mis próximos viajes</h3>
                </div>

                @if(Auth::user()->reservas->count() > 0)
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 border-0 py-3">Destino</th>
                                        <th class="border-0 py-3 text-center">Fecha Reserva</th>
                                        <th class="border-0 py-3 text-center">Precio</th>
                                        <th class="border-0 py-3 text-end pe-4">Gestión</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(Auth::user()->reservas as $reserva)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $reserva->vacation->foto ? $reserva->vacation->foto->getPath() : asset('assets/img/sin-foto.jpg') }}" 
                                                        class="rounded-3 me-3" 
                                                        style="width: 60px; height: 60px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $reserva->vacation->titulo }}</div>
                                                        <div class="text-muted small">
                                                            <i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $reserva->vacation->pais }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center text-muted">
                                                {{ $reserva->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-primary fs-5">{{ number_format($reserva->vacation->precio, 0) }}€</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('vacation.show', $reserva->idvacation) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                            onclick="confirmarCancelacion('{{ route('reserva.destroy', $reserva->id) }}')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5 bg-white shadow-sm rounded-4 border border-dashed">
                        <div class="mb-3">
                            <i class="fa-solid fa-plane-departure display-1 text-light"></i>
                        </div>
                        <h5 class="text-muted">¿Aún no has planeado nada?</h5>
                        <p class="text-muted small">Descubre los mejores destinos y reserva tu próxima aventura.</p>
                        <a href="{{ route('main.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">Explorar Catálogo</a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Formulario oculto para eliminar reserva --}}
    <form id="form-cancelar" action="" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

@endsection

@section('scripts')
    <script>
        function confirmarCancelacion(url) {
            if (confirm('¿Seguro que quieres cancelar tu reserva? Otros viajeros podrían quitarte el sitio.')) {
                const form = document.getElementById('form-cancelar');
                form.setAttribute('action', url);
                form.submit();
            }
        }
    </script>
@endsection