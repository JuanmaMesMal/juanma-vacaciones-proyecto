<!doctype html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Viajes-Juanma')</title>
    <link rel="icon" type="image/x-icon" href="{{ url('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ url('assets/css/main-agency.css?r=' . rand(1, 10000)) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  </head>

  <body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('main.index') }}">
                <i class="fa-solid fa-plane me-4"></i>@yield('navbar', 'JVIAJES')
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navContent">

                
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">

                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('main.index') }}">Inicio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="dropVacations" role="button" data-bs-toggle="dropdown">
                            Destinos
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('vacation.index') }}"><i class="fa-solid fa-list me-2"></i>Ver Catálogo</a></li>
                            @auth
                                @if(Auth::user()->isAdvanced())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('vacation.create') }}">
                                                <i class="fa-solid fa-plus me-2"></i>Publicar Oferta
                                            </a>
                                        </li>
                                    @endif
                            @endauth
                        </ul>
                    </li>
                    @auth
                        @if(Auth::user()->isAdvanced())
                            <li class="me-2 nav-item">
                                <a class="nav-link btn btn-primary text-black" href="{{ route('vacation.create') }}">
                                    <i class="fa-solid fa-plus me-1"></i> Publicar Viaje
                                </a>
                            </li>
                        @endif
                    @endauth
                    <li class ="nav-item">
                        @guest
                        <a class="btn btn-success" href="{{ route('login') }}">Login</a>

                        @else
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</a>
                        </form>


                        @endguest
                    </li>
                    @auth
                        <li>
                            <a class="btn btn-success" href="{{ route('home') }}" style="margin-left: .5rem;">Info</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">

        @if($errors->has('general'))
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fa-solid fa-circle-exclamation me-3 fs-4"></i>
                <div>{{ $errors->first('general') }}</div>
            </div>
        @endif

        @if(session('general'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="fa-solid fa-circle-check me-3 fs-4"></i>
                <div>{{ session('general') }}</div>
            </div>
        @endif

        @yield('content')

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>

</html>