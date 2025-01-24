<?php

namespace App\Http\Controllers\Productividad;

use App\Http\Controllers\Controller;
use App\Models\Bajas\Baja;
use App\Models\Holidays\HolidaysPetitions;
use App\Models\Jornada\Jornada;
use App\Models\Tasks\LogTasks;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
            $data = $this->nota($user->id,$fechaInicio);
            $nota = $data['puntuacion'];
            $bajas = $data['bajas'];
            // Almacena la información en el array de productividad
            $productividadUsuarios[] = [
                'id' => $user->id,
                'nombre' => $user->name,
                'productividad' => round($totalProductividad, 2), // Redondea a 2 decimales
                'tareasfinalizadas' => $totalTareas,
                'horasReales' => $this->convertirTiempo($totalRealTime),
                'horasEstimadas' => $this->convertirTiempo($totalEstimatedTime),
                'tareas' => $tareasFinalizadas ,// Agregar las tareas finalizadas
                'nota' => $nota,
                'bajas' => $bajas,
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

    public function nota($userId,$fechaInicio){
        $fechaInicio = Carbon::parse($fechaInicio)->subMonth()->startOfMonth()->startOfDay();
        $productividad = $this->productividadMesAnterior($userId , $fechaInicio);
        $horasMes = $this->tiempoProducidoMesanterior($userId ,$fechaInicio);
        $partes = explode(':', $horasMes);
        $horas = $partes[0];
        $minutos = $partes[1];
        $segundos = $partes[2];
        $totalHorasproducidas = $horas + $minutos/60 + $segundos/3600;

        $startOfMonth = $fechaInicio;
        $endOfMonth = $fechaInicio->copy()->endOfMonth();
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        $diasLaborables = $period->filter(function (Carbon $date) {
            return !$date->isWeekend(); // Retorna true si NO es sábado ni domingo
        });

        $diasReales = $diasLaborables->count();
        $vacaciones = $this->vacaciones($startOfMonth, $endOfMonth, $userId);
        $bajas = $this->bajas($userId,$startOfMonth, $endOfMonth);
        $festivos = $this->festivos($startOfMonth, $endOfMonth,$diasLaborables );

        $diasTotales = $diasReales - $vacaciones - $bajas - $festivos;
        $horasTotales = $diasTotales * 7;

        if($totalHorasproducidas >= ($horasTotales*0.5)){
            $putuacionProductividad = $productividad/20;
        }else{
            $putuacionProductividad = 0;
        }

        $putuacionHoras = (($totalHorasproducidas*100)/$horasTotales)/20;

        //dd($productividad,$putuacionProductividad, $putuacionHoras);

        $putuacion = $putuacionProductividad + $putuacionHoras;

        $data = [
            'puntuacion' => $putuacion,
            'bajas' => $bajas,
        ];
        return $data;
    }

    public function productividadMesAnterior($id , $fechaInicio){
        $month = $fechaInicio;
        $year = $month->year;

        $tareasFinalizadas = Task::where('admin_user_id', $id)
        ->where('task_status_id', 3)
        ->whereMonth('updated_at', $month)
        ->whereYear('updated_at', $year)
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
        return $totalProductividad;
    }

    public function tiempoProducidoMesanterior($id,$fechaInicio)
    {
        $mes = $fechaInicio;
        $tiempoTotalMes = 0;

        // Obtener todas las tareas del usuario en el mes actual
        $tareasMes = LogTasks::where('admin_user_id', $id)
            ->whereYear('date_start', $mes->year)
            ->whereMonth('date_start', $mes->month)
            ->get();

        foreach($tareasMes as $tarea) {
            if ($tarea->status == 'Pausada') {
                $tiempoInicio = Carbon::parse($tarea->date_start);
                $tiempoFinal = Carbon::parse($tarea->date_end);
                $tiempoTotalMes += $tiempoFinal->diffInSeconds($tiempoInicio);
            }
        }

        // Formatear el tiempo total en horas, minutos y segundos
        $hours = floor($tiempoTotalMes / 3600);
        $minutes = floor(($tiempoTotalMes % 3600) / 60);
        $seconds = $tiempoTotalMes % 60;

        $result = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        // Calcular el porcentaje del tiempo trabajado en relación al total
        $totalHorasMensuales = 7 * 20; // Ejemplo de 20 días laborales y 7 horas diarias
        $horas_mes_porcentaje = $hours + ($minutes / 60);
        $porcentaje = ($horas_mes_porcentaje / $totalHorasMensuales) * 100;

        $data = [
            'horas' => $result,
            'porcentaje' => $porcentaje
        ];

        return $result;
    }

    public function festivos($startOfMonth, $endOfMonth, $diasLaborables)
    {
        // Obtener fechas de jornadas iniciadas en el período
        $jornadas = Jornada::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->distinct()
            ->pluck('created_at')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });

        // Filtrar días laborales sin jornada
        $diasSinJornada = $diasLaborables->filter(function (Carbon $date) use ($jornadas) {
            return !$jornadas->contains($date->format('Y-m-d'));
        });

        return $diasSinJornada->count();
    }

    public function vacaciones($ini, $fin, $id){
        $vacaciones = HolidaysPetitions::where('admin_user_id', $id)
        ->whereDate('from','>=', $ini)
        ->whereDate('to','<=', $fin)
        ->where('holidays_status_id', 1)
        ->get();

        $dias = $vacaciones->sum('total_days');

        return $dias;
    }

    public function bajas($id, $ini, $fin)
    {
        $diasTotales = 0;

        // Obtener las bajas del usuario dentro del rango especificado
        $bajas = Baja::where('admin_user_id', $id)
            ->where(function ($query) use ($ini, $fin) {
                $query->whereBetween('inicio', [$ini, $fin])
                    ->orWhereBetween('fin', [$ini, $fin])
                    ->orWhere(function ($query) use ($ini, $fin) {
                        $query->where('inicio', '<=', $ini)
                                ->where('fin', '>=', $fin);
                    });
            })->get();

        foreach ($bajas as $baja) {
            $inicioBaja = Carbon::parse($baja->inicio);
            $finBaja = Carbon::parse($baja->fin) ?? Carbon::now();

            // Ajustar fechas al intervalo especificado
            $fechaInicio = $inicioBaja->greaterThan($ini) ? $inicioBaja : Carbon::parse($ini);
            $fechaFin = $finBaja->lessThan($fin) ? $finBaja : Carbon::parse($fin);

            // Crear un período para las fechas ajustadas
            $period = CarbonPeriod::create($fechaInicio, $fechaFin);

            // Contar solo los días laborables en el período
            $diasLaborables = $period->filter(function (Carbon $date) {
                return !$date->isWeekend(); // Excluir sábados y domingos
            })->count();

            $diasTotales += $diasLaborables;
        }

        return $diasTotales;
    }
}
