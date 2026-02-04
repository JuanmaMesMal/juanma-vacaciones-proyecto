<?php

namespace App\Http\Controllers;

use App\Models\Vacation;
use App\Models\Tipo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MainController extends Controller
{

    private function limpiarCampo($campo): string {
        return $this->limpiarInput($campo, ['id', 'titulo', 'idtipo', 'precio' ,'pais' ,'descripcion']);
    }

    private function limpiarOrden($orden): string {
        return $this->limpiarInput($orden, ['desc', 'asc']);
    }

    private function limpiarInput($input, array $array): string {
        $valor = $array[0];
        if(in_array($input, $array)) {
            $valor = $input;
        }
        return $valor;
    }

    function indexOld(): View {
        $vacations = Vacation::orderBy('precio', 'desc')->get();
        
        $array = ['vacations' => $vacations, ]; 

        return view('main.index', $array); 

    }

    public function index(Request $request) {
        $campo = $this->limpiarCampo($request->campo);
        $orden = $this->limpiarOrden($request->orden);
        $q = $request->q;
        $idtipo = $request->idtipo;

        // 2. Construcción de la consulta
        $query = Vacation::query()->with(['tipo', 'foto']);

        // Filtro por Tipo (Categoría)
        if($idtipo != null) {
            $query->where('idtipo', '=', $idtipo);
        }

        if($q != null) {
            $query->where(function($sq) use ($q) {
                $sq->where('titulo', 'like', '%' . $q . '%')
                   ->orWhere('descripcion', 'like', '%' . $q . '%')
                   ->orWhere('pais', 'like', '%' . $q . '%')
                   ->orWhere('precio', 'like', '%' . $q . '%')
                   ->orWhere('id', 'like', '%' . $q . '%');
            });
        }

       
        if($campo == 'descripcion') {
            $query->orderByRaw("char_length(descripcion) $orden");
        } else {
            $query->orderBy($campo, $orden);
        }

        $vacations = $query->paginate(6)->withQueryString();
        
        $tipos = Tipo::pluck('nombre', 'id');

        return view('main.index', [
            'vacations'  => $vacations,
            'tipos'      => $tipos,
            'campo'      => $campo,
            'orden'      => $orden,
            'idtipo'     => $idtipo,
            'q'          => $q,
            'urlDestino' => route('main.index', $request->except('page'))
        ]);
    }
    
}
