<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alerts\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{


public function getUserAlerts()
{
    // Obtener alertas del usuario autenticado
    $alerts = Alert::where('admin_user_id', auth()->id())
    ->with('stage') // Relacionamos las alertas con la etapa
    ->orderBy('stage_id') // Ordenar por el `stage_id`
    ->get()
    ->groupBy('stage_id'); // Agrupar por `stage_id`

    return response()->json(data: $alerts);
}

}
