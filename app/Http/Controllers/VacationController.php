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
        $vacation = Vacation::all();//select * from vacation;
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
            // 1. Guardar la vacación primero para obtener su ID
            $vacation = new Vacation($request->validated());
            $result = $vacation->save();

            // 2. Si se guardó correctamente, gestionamos la imagen con la función upload
            if ($result && $request->hasFile('image')) {
                
                // Llamamos a tu función privada pasándole el ID recién creado
                $path = $this->upload($request, $vacation->id);

                // 3. Si upload devolvió una ruta válida, guardamos en la tabla 'foto'
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

    private function upload($request, $id) { // Quitamos el typehint Request si da conflicto con tu VacationCreateRequest
        if (!$request->hasFile('image')) {
            return null;
        }

        $image = $request->file('image');

        if (!$image->isValid()) {
            return null;
        }

        // Generamos el nombre: "ID.extension" (ej: 2.png)
        $fileName = $id . '.' . $image->getClientOriginalExtension();
        
        // Guardamos usando storeAs para forzar el nombre legible
        // Se guardará en storage/app/public/images/
        return $image->storeAs('images', $fileName, 'public');
    }

    function edit(Vacation $vacation): View {
        $tipos = Tipo::pluck('nombre', 'id');
        return view('vacation.edit', ['vacation' => $vacation, 'tipos' => $tipos]);
    }

    public function update(VacationEditRequest $request, Vacation $vacation): RedirectResponse {
        try {
            // 1. Actualizar los campos básicos de la vacación
            $vacation->update($request->validated());

            // Buscamos si ya tiene una foto asociada
            $foto = Foto::where('idvacation', $vacation->id)->first();

            // 2. Si el usuario marcó "eliminar imagen"
            if ($request->has('delete_image') && $foto) {
                if (Storage::disk('public')->exists($foto->path)) {
                    Storage::disk('public')->delete($foto->path);
                }
                $foto->delete(); // Borramos el registro de la tabla foto
            }

            // 3. Si se sube una nueva imagen
            if ($request->hasFile('image')) {
                // Borramos la imagen física anterior si existía
                if ($foto && Storage::disk('public')->exists($foto->path)) {
                    Storage::disk('public')->delete($foto->path);
                }

                // Usamos tu función upload para guardar la nueva
                $newPath = $this->upload($request, $vacation->id);

                // Actualizamos o creamos el registro en la tabla 'foto'
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
        // $asignaturas = Asignatura::all();
        // $SentObservacion = session()->get('SentObservacion');
        // if($SentObservacion == null) {
        //     $SentObservacion = new SentObservacion();
        //     session()->put('SentObservacion', $SentObservacion);
        // }
        $year = Carbon::now()->year;
        // return view('vacation.show', [
        //     'asignaturas'     => $asignaturas, 
        //     'vacation'        => $vacation, 
        //     'year'            => $year, 
        //     'SentObservacion' => $SentObservacion]);
        
        return view('vacation.show', [
            'vacation'        => $vacation, 
            'year'            => $year,]);
    }

    function destroy(Vacation $vacation) {
       try {
            // busco si tiene una foto asociada
            $foto = Foto::where('idvacation', $vacation->id)->first();

            if ($foto) {
                // 2. Borrar el archivo físico del disco 'public'
                if (Storage::disk('public')->exists($foto->path)) {
                    Storage::disk('public')->delete($foto->path);
                }
                // 3. Borrar el registro en la tabla 'foto'
                $foto->delete();
            }

            // 4. Ahora ya podemos borrar la vacación sin errores de dependencia
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
