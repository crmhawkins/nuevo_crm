<?php

namespace App\Http\Controllers\Llamadas;

use App\Http\Controllers\Controller;
use App\Models\Llamadas\Llamada;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LlamadaController extends Controller
{

    public function index(Request $request){

        // Variables de filtro
        $buscar = $request->input('buscar');
        $selectedGestor = $request->input('selectedGestor');
        $dateFrom = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $dateTo = Carbon::parse($request->input('fecha_fin'))->endOfDay();
        $sortColumn = $request->input('sortColumn', 'created_at'); // Columna por defecto
        $sortDirection = $request->input('sortDirection', 'desc'); // Dirección por defecto
        $perPage = $request->input('perPage', 10);

        // Cargando datos estáticos
        $gestores = User::Where('access_level_id',4)->where('inactive', 0)->get();


        // Construcción de la consulta principal
        $query = Llamada::query();

        // Aplicar filtros
        if ($selectedGestor) {
            $query->where('admin_user_id', $selectedGestor);
        }
        if ($dateFrom) {
            $query->where('created_at','>', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at','<', $dateTo);
        }

        if ($buscar = $request->input('buscar')) {

            $query->Where(function ($subQuery)  use ($buscar) {
                $subQuery->whereHas('client', function ($q) use ($buscar) {
                    $q->where('name','like','%'. $buscar .'%')
                        ->orWhere('company', 'like', '%' . $buscar . '%');
                })
                ->orwhereHas('kit', function ($q) use ($buscar) {
                    $q->where('cliente','like','%'. $buscar . '%');
                });
            });
        }

        $query->orderBy($sortColumn, $sortDirection);
        // Aplicar ordenación y paginación
        $llamadas = $perPage === 'all' ? $query->get() : $query->paginate(is_numeric($perPage) ? $perPage : 10);



        return view('llamadas.index', compact(
            'llamadas',
            'gestores',
            'selectedGestor',
            'dateFrom',
            'dateTo',
            'sortColumn',
            'sortDirection',
            'perPage',
            'buscar',
        ));

    }

}
