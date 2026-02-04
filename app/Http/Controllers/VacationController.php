<?php

namespace App\Http\Controllers;

use App\Custom\SentComentario;
use App\Http\Requests\VacationCreateRequest;
use App\Http\Requests\VacationEditRequest;
use App\Models\Vacation;
use App\Models\Tipo;
use App\Models\Foto;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VacationController extends Controller
{

    public function __construct() {
        // los usuarios advanced o superior tienen acceso a...
        $this->middleware('advanced')->only(['create', 'store', 'edit', 'update', 'destroy']); 
    }

    function index(): View {
        $vacation = Vacation::all();
        $array = ['vacations' => $vacation];
        return view('vacation.index', $array);
    }

    function create(): View {
        $tipos = Tipo::pluck('nombre', 'id');
        $fotos = Foto::pluck('id', 'path');
        return view('vacation.create', ['tipos' => $tipos, 'fotos' => $fotos]);
    }

    function store(VacationCreateRequest $request): RedirectResponse {
        $result = false;
        $message = '';

        try {
            $vacation = new Vacation($request->validated());
            $result = $vacation->save();

            if ($result && $request->hasFile('image')) {
                
                $path = $this->upload($request, $vacation->id);

                if ($path) {
                    Foto::create([
                        'idvacation' => $vacation->id,
                        'path'       => $path
                    ]);
                }
            }

            $message = 'El anuncio de vacaciones ha sido añadido correctamente.';

        } catch (UniqueConstraintViolationException $e) {
            $message = 'No puede haber dos anuncios con el mismo nombre.';
            $result = false;
        } catch (QueryException $e) {
            $message = 'Error en la base de datos: Revisa los campos obligatorios.';
            $result = false;
        } catch (\Exception $e) {
            $message = 'Se ha producido un error: ' . $e->getMessage();
            $result = false;
        }

        $messageArray = ['general' => $message];

        return $result 
            ? redirect()->route('main.index')->with($messageArray)
            : back()->withInput()->withErrors($messageArray);
    }

    private function upload($request, $id) { 
        if (!$request->hasFile('image')) {
            return null;
        }

        $image = $request->file('image');

        if (!$image->isValid()) {
            return null;
        }

        $fileName = $id . '.' . $image->getClientOriginalExtension();
        
        return $image->storeAs('images', $fileName, 'public');
    }

    function edit(Vacation $vacation): View {
        $tipos = Tipo::pluck('nombre', 'id');
        return view('vacation.edit', ['vacation' => $vacation, 'tipos' => $tipos]);
    }

    public function update(VacationEditRequest $request, Vacation $vacation): RedirectResponse {
        try {
            $vacation->update($request->validated());

            $foto = Foto::where('idvacation', $vacation->id)->first();

            if ($request->has('delete_image') && $foto) {
                if (Storage::disk('public')->exists($foto->path)) {
                    Storage::disk('public')->delete($foto->path);
                }
                $foto->delete(); 
            }

            if ($request->hasFile('image')) {
                if ($foto && Storage::disk('public')->exists($foto->path)) {
                    Storage::disk('public')->delete($foto->path);
                }

                $newPath = $this->upload($request, $vacation->id);

                if ($foto) {
                    $foto->update(['path' => $newPath]);
                } else {
                    Foto::create([
                        'idvacation' => $vacation->id,
                        'path' => $newPath
                    ]);
                }
            }

            return redirect()->route('main.index')->with('general', 'Anuncio actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'general' => 'Error al actualizar: ' . $e->getMessage()
            ]);
        }
    }

    function show(Vacation $vacation): View {

        $year = Carbon::now()->year;

        return view('vacation.show', [
            'vacation'        => $vacation, 
            'year'            => $year,]);
    }

    function destroy(Vacation $vacation) {
       try {
            $foto = Foto::where('idvacation', $vacation->id)->first();

            if ($foto) {
                if (Storage::disk('public')->exists($foto->path)) {
                    Storage::disk('public')->delete($foto->path);
                }
                $foto->delete();
            }

            $result = $vacation->delete();
            $message = 'El anuncio y su imagen han sido eliminados correctamente.';
        } catch(\Exception $e) {
            $result = false;
            $message = 'El anuncio vacacional no ha podido borrarse.';
        }
        $messageArray = [
            'general' => $message
        ];
        if($result) {
            return redirect()->route('main.index')->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }

    function tipo(Tipo $tipo): View {

        $vacations = $tipo->vacations()->paginate(6)->withQueryString();

        return view('vacation.tipo', [
            'tipo'      => $tipo, 
            'vacations' => $vacations
            ]);
    }
}
