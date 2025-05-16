<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalvineitorController extends Controller
{
    public function index()
    {

        $now = Carbon::now(); // Usa la hora actual

        $diaSemana = $now->dayOfWeek; // 0 (domingo) a 6 (sábado)
        $hora = $now->hour;

        if ($diaSemana >= 1 && $diaSemana <= 5 && $hora >= 7 && $hora <= 19) {
            return response()->json([
                'success' => 'OK'
            ], 200);
        } else {
            return response()->json([
                'success' => 'Fuera de horario permitido'
            ], 400);
        }
        # Retorna 200
        // return response()->json([
        //     'sucess' => 'OK'
        // ], 200);
    }
}
