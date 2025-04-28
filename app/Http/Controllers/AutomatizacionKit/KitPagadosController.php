<?php

namespace App\Http\Controllers\AutomatizacionKit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KitDigital;

class KitPagadosController extends Controller
{
    public function getContratos()
    {
        $fechaLimite = now()->subDays(335)->format('Y-m-d');
    
        $data = KitDigital::where('fecha_actualizacion', '<=', $fechaLimite)
            ->where('estado', 11)
            ->select([
                'id',
                'contratos',
                'estado',
                'fecha_actualizacion',
            ])->get();
        
        return $data;
    }
    
    public function viewPagados()
    {
        $resultados = $this->getContratos();

        return view('kitDigital.kitPagados', compact('resultados'));
    }
}
