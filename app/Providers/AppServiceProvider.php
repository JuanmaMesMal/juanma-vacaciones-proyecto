<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Laravel => Tailwind, no Bootstrap
        //las clases que se usan en el paginador
        // <etiqueta class="bootstrap tailwind">contenido</etiqueta>
        Paginator::useBootstrap();
    }
}
