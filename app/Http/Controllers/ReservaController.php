<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{

    public function store(Request $request): RedirectResponse
    {
        // 1. Validación básica
        $request->validate([
            'idvacation' => 'required|exists:vacation,id',
        ]);

        $idvacation = $request->idvacation;
        $iduser = Auth::id();

        // 2. Seguridad: Verificar si el viaje ya está reservado por CUALQUIER usuario
        $yaReservado = Reserva::where('idvacation', $idvacation)->exists();

        if ($yaReservado) {
            return back()->withErrors([
                'general' => 'Lo sentimos, este destino acaba de ser reservado por otro usuario.'
            ]);
        }

        // 3. Crear la reserva
        try {
            Reserva::create([
                'idvacation' => $idvacation,
                'iduser' => $iduser,
            ]);

            return back()->with('general', '¡Reserva confirmada! Prepara tus maletas.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'general' => 'Hubo un error al procesar tu reserva. Inténtalo de nuevo.'
            ]);
        }
    }

    public function destroy(Reserva $reserva): RedirectResponse
    {
        // Seguridad: Solo el dueño de la reserva puede cancelarla
        if ($reserva->iduser !== Auth::id()) {
            return back()->withErrors(['general' => 'No tienes permiso para cancelar esta reserva.']);
        }

        $reserva->delete();

        return back()->with('general', 'Tu reserva ha sido cancelada correctamente.');
    }
}