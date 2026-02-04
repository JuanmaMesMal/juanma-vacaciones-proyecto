<?php

namespace App\Http\Controllers;

use App\Custom\SentComentario;
use App\Models\Comentario;
use App\Models\User;
use App\Models\Reserva;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComentarioController extends Controller {
    
    function create(): View {
    }

    function destroy(Comentario $comentario): RedirectResponse {
        try {
            $result = $comentario->delete();
            $message = 'Se ha eliminado la observación';
        } catch(\Exception $e) {
            dd($e);
            $result = false;
            $message = 'La comentario no ha podido borrarse correctamente.';
        }
        $messageArray = [
            'general' => $message
        ];
        if($result) {
            return back()->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }

    }

    function edit(Comentario $comentario): View {
        $vacation = $comentario->vacation; 
        return view('comentario.edit', [
            'comentario' => $comentario, 
            'vacation'   => $vacation
        ]);
    }
    
    function index(): View {
    }

    function show(Comentario $comentario): View {
    }

    public function store(Request $request) {
        $request->validate([
            'content' => 'required|min:5|max:1000',
            'idvacation' => 'required|exists:vacation,id'
        ]);

        // 2. Seguridad: ¿Tiene el correo verificado?
        if (!auth()->user()->hasVerifiedEmail()) {
            return back()->withErrors(['general' => 'Debes verificar tu correo para poder comentar.']);
        }

        $haReservado = Reserva::where('iduser', auth()->id())
                        ->where('idvacation', $request->idvacation)
                        ->exists();

        if (!$haReservado) {
            return back()->withErrors(['general' => 'Solo puedes comentar en viajes que hayas reservado previamente.']);
        }

        //  Guardamos en la base de datos
        $comentario = new Comentario();
        $comentario->idvacation = $request->idvacation;
        $comentario->texto = $request->content;
        $comentario->iduser = auth()->id();
        $comentario->save();

        // Gestión de la sesión para edición/borrado rápido
        $sentComentario = session()->get('sentComentario', new SentComentario());
        $sentComentario->addComentario($comentario);
        session()->put('sentComentario', $sentComentario); 

        return back()->with('general', '¡Gracias por tu opinión! Tu comentario ha sido añadido.');
    }
    
    public function update(Request $request, Comentario $comentario): RedirectResponse {
        $request->validate([
            'texto' => 'required|string|min:5',
        ]);

        //  Comprobar sesión (SentComentario) y Autoría
        $sentComentario = session()->get('sentComentario');
        
        if (!$sentComentario || !$sentComentario->isComentario($comentario)) {
            return redirect()->route('main.index')->withErrors([
                'general' => 'No puedes editar este comentario: la sesión ha expirado o no tienes permiso.'
            ]);
        }

        if ($comentario->iduser != auth()->id()) {
            return redirect()->route('main.index')->withErrors([
                'general' => 'Acción no autorizada.'
            ]);
        }

        $result = false;
        try {
            $comentario->texto = $request->texto;

            if ($comentario->isDirty()) { 
                $result = $comentario->save();
                $message = 'El comentario ha sido editado correctamente.';
            } else {
                $result = true; 
                $message = 'No se realizaron cambios en el comentario.';
            }
            
        } catch(\Exception $e) {
            \Log::error("Error editando comentario: " . $e->getMessage());
            $message = 'Se ha producido un error al intentar guardar los cambios.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('vacation.show', $comentario->idvacation)->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }

}
