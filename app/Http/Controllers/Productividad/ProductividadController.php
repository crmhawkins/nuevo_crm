<?php

namespace App\Http\Controllers\Productividad;

use App\Http\Controllers\Controller;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductividadController extends Controller
{
    public function index(Request $request)
    {
        // Obtiene los usuarios activos con nivel de acceso 5
        $usuarios = User::where('inactive', 0)->where('access_level_id', 5)->get();

        // Obtiene las fechas del rango o establece valores predeterminados
        $fechaInicio = $request->input('fecha_inicio') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $request->input('fecha_fin') ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        // Convierte las fechas a objetos Carbon
        $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($fechaFin)->endOfDay();

        $productividadUsuarios = []; // Array para almacenar la productividad de cada usuario

        foreach ($usuarios as $user) {
            $tareasFinalizadas = Task::where('admin_user_id', $user->id)
                ->where('task_status_id', 3)
                ->whereBetween('updated_at', [$fechaInicio, $fechaFin])
                ->whereRaw("TIME_TO_SEC(real_time) > 1740") // 29 minutos en segundos
                ->get();

            $totalProductividad = 0;
            $totalTareas = $tareasFinalizadas->count();
            $totalEstimatedTime = 0;
            $totalRealTime = 0;

            foreach ($tareasFinalizadas as $tarea) {
                // Parse estimated and real times into total minutes
                $totalEstimatedTime += $this->parseFlexibleTime($tarea->estimated_time);
                $totalRealTime += $this->parseFlexibleTime($tarea->real_time);
            }

            // Calculate the total productivity as a percentage
            if ($totalRealTime > 0) {
                $totalProductividad = ($totalEstimatedTime / $totalRealTime) * 100;
            } else {
                $totalProductividad = 0; // Set to 0 if no real time to avoid division by zero
            }

            // Set productivity to 0 if no tasks
            $totalProductividad = $totalTareas > 0 ? $totalProductividad : 0;

            // Almacena la información en el array de productividad
            $productividadUsuarios[] = [
                'id' => $user->id,
                'nombre' => $user->name,
                'productividad' => round($totalProductividad, 2), // Redondea a 2 decimales
                'tareasfinalizadas' => $totalTareas,
                'horasReales' => $this->convertirTiempo($totalRealTime),
                'horasEstimadas' => $this->convertirTiempo($totalEstimatedTime),
                'tareas' => $tareasFinalizadas // Agregar las tareas finalizadas
            ];
        }

        // Pasa el array de productividad a la vista
        return view('productividad.index', compact('productividadUsuarios', 'fechaInicio', 'fechaFin'));
    }

    public function parseFlexibleTime($time)
    {
        list($hours, $minutes, $seconds) = explode(':', $time);
        return ($hours * 60) + $minutes + ($seconds / 60); // Convert to total minutes
    }

    function convertirTiempo($minutos)
    {
        $horas = floor($minutos / 60);            // Divide minutos entre 60 para obtener las horas
        $minutosRestantes = $minutos % 60;        // Usa módulo para obtener los minutos restantes
        $segundos = ($minutos - floor($minutos)) * 60;  // Calcula los segundos

        return sprintf("%02d:%02d:%02d", $horas, $minutosRestantes, $segundos);
    }
}
