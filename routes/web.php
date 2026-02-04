<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\VacationController;

Route::get('/', [MainController::class, 'index'])->name('main.index');

Route::resource('vacation', VacationController::class);
Route::get('vacation/tipo/{tipo}', [VacationController::class, 'tipo'])->name('vacation.tipo');


Route::resource('comentario', ComentarioController::class);

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () { // para que solo los usarios logueados puedan reservar
    Route::post('/reserva', [ReservaController::class, 'store'])->name('reserva.store');
    Route::delete('/reserva/{reserva}', [ReservaController::class, 'destroy'])->name('reserva.destroy');
});