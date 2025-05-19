<?php

namespace App\Http\Controllers;

use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\Gasto;
use App\Models\Accounting\Ingreso;
use App\Models\Accounting\UnclassifiedExpenses;
use App\Models\Accounting\UnclassifiedIncome;
use App\Models\Alerts\Alert;
use App\Models\Alerts\AlertStatus;
use App\Models\Bajas\Baja;
use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\Holidays\Holidays;
use App\Models\Holidays\HolidaysPetitions;
use App\Models\HoursMonthly\HoursMonthly;
use App\Models\Invoices\Invoice;
use App\Models\Jornada\Jornada;
use App\Models\Jornada\Pause;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\Llamadas\Llamada;
use App\Models\Other\BankAccounts;
use App\Models\Petitions\Petition;
use App\Models\Logs\LogActions;
use App\Models\Notes\Note;
use App\Models\ProductividadMensual;
use App\Models\Projects\Project;
use App\Models\Tasks\LogTasks;
use App\Models\Tasks\Task;
use App\Models\Todo\Todo;
use App\Models\Todo\TodoUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Stripe\BankAccount;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $id = Auth::user()->id;
        $acceso = Auth::user()->access_level_id;
        $user = User::find($id);
        $users = User::where('inactive', 0)->get();
        $to_dos = $user
            ->todos()
            ->where('finalizada', false)
            ->whereDoesntHave('todoUsers', function ($query) use ($user) {
                $query->where('admin_user_id', $user->id)->where('completada', true);
            })
            ->get();
        $to_dos_finalizados = $user->todos->where('finalizada', true);
        $timeWorkedToday = $this->calculateTimeWorkedToday($user);
        $jornadaActiva = $user->activeJornada();
        $llamadaActiva = $user->activeLlamada();
        $events = $user->eventos->map(function ($event) {
            return $event->nonNullAttributes(); // Usa el método que definimos antes
        });
        $pausaActiva = null;
        if ($jornadaActiva) {
            $pausaActiva = $jornadaActiva->pausasActiva();
        }
        $unclassifiedIncomes =[];

        switch ($acceso) {
            case 1:
                // Obtener las fechas de la solicitud, o asignar fechas predeterminadas si no están presentes
                $fechaInicio = $request->input('fecha_inicio') ?? date('Y-m-01'); // Primer día del mes actual
                $fechaFin = $request->input('fecha_fin') ?? date('Y-m-d'); // Día actual
                $produccion = $this->produccion($fechaInicio, $fechaFin);
                $gestion = $this->gestion($fechaInicio, $fechaFin);
                $contabilidad = $this->contabilidad($fechaInicio, $fechaFin);
                $comercial = $this->comercial($fechaInicio, $fechaFin);
                // Validar las fechas
                if (!$fechaInicio || !$fechaFin) {
                    return redirect()->back()->with('error', 'Por favor selecciona un rango de fechas válido.');
                }
                // Buscar los ingresos en el rango de fechas
                $ingresos = Invoice::whereBetween('created_at', [$fechaInicio, $fechaFin])
                    ->whereIn('invoice_status_id', [1, 3, 4])
                    ->get();
                // Buscar los gastos en el rango de fechas
                $gastos = Gasto::whereBetween('received_date', [$fechaInicio, $fechaFin])
                    ->where(function ($query) {
                        $query->where('transfer_movement', 0)->orWhereNull('transfer_movement');
                    })
                    ->get();

                // Buscar los gastos asociados en el rango de fechas
                $gastosAsociados = AssociatedExpenses::whereBetween('received_date', [$fechaInicio, $fechaFin])->get();

                // Calcular la cantidad de cada tipo
                $ingresosCount = $ingresos->count();
                $gastosCount = $gastos->count();
                $gastosAsociadosCount = $gastosAsociados->count();

                // Calcular beneficios
                $totalIngresos = $ingresos->sum('total');
                $totalGastosComunes = $gastos->sum('quantity');
                $totalGastosSociados = $gastosAsociados->sum('quantity');
                $totalGastos = $totalGastosComunes + $totalGastosSociados;
                $beneficios = $totalIngresos - $totalGastos;

                $clientes = Client::where('is_client', true)->get();
                $budgets = Budget::where('admin_user_id', $id)->get();
                $projects = Project::where('admin_user_id', $id)->get();
                $tareas = Task::where('gestor_id', $id)->get();
                $ingresos = 0;
                $gastos = 0;
                $gastosAsociados = 0;

                return view('dashboards.dashboard', compact('user', 'tareas', 'to_dos', 'budgets', 'projects', 'clientes', 'users', 'events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva', 'llamadaActiva', 'totalIngresos', 'totalGastosComunes', 'totalGastosSociados', 'beneficios', 'to_dos_finalizados', 'produccion', 'gestion', 'contabilidad', 'comercial'));
            case 2:
                $clientes = Client::where('is_client', true)->get();
                $budgets = Budget::where('admin_user_id', $id)->get();
                $projects = Project::where('admin_user_id', $id)->get();
                $tareas = Task::where('gestor_id', $id)->get();
                $horasSemanales = $this->horasSemanales();
                return view('dashboards.dashboard_gestor', compact('user', 'tareas', 'to_dos', 'budgets', 'projects', 'clientes', 'users', 'events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva', 'llamadaActiva', 'to_dos_finalizados', 'horasSemanales'));
            case 3:
                $clientes = Client::where('is_client', true)->get();
                $budgets = Budget::where('admin_user_id', $id)->get();
                $projects = Project::where('admin_user_id', $id)->get();
                $tareas = Task::where('gestor_id', $id)->get();
                $horasSemanales = $this->horasSemanales();

                $unclassifiedIncomes = UnclassifiedIncome::where('status', 0)->get();
                $unclassifiedExpenses = UnclassifiedExpenses::where('status', 0)->get();

                // Ingresos
                foreach ($unclassifiedIncomes as $income) {
                    $relacionesMapeadas = [];

                    if ($income->relacion && $income->relacion != '[]') {
                        $relacionesCrudas = is_string($income->relacion) ? json_decode($income->relacion, true) : $income->relacion;

                        foreach ($relacionesCrudas as $rel) {
                            switch ($rel['tabla']) {
                                case 1:
                                    $relacion = Invoice::find($rel['id']);
                                    $tabla = 'Factura';
                                    break;
                                case 2:
                                    $relacion = Ingreso::find($rel['id']);
                                    $tabla = 'Ingreso';
                                    break;
                                case 3:
                                    $relacion = Gasto::find($rel['id']);
                                    $tabla = 'Gasto';
                                    break;
                                case 4:
                                    $relacion = AssociatedExpenses::find($rel['id']);
                                    $tabla = 'Gasto asociado';
                                    break;
                                case 5:
                                    $relacion = Budget::find($rel['id']);
                                    $tabla = 'Presupuesto';
                                    break;
                                default:
                                    $relacion = null;
                                    $tabla = null;
                            }

                            if ($relacion) {
                                $relacionesMapeadas[] = [
                                    'modelo' => $relacion,
                                    'tabla' => $tabla,
                                    'id' => $income->id,
                                ];
                            }
                        }
                    }

                    $income->relaciones = $relacionesMapeadas;
                }

                // Gastos
                foreach ($unclassifiedExpenses as $expense) {
                    $relacionesMapeadas = [];

                    if ($expense->relacion && $expense->relacion != '[]') {
                        $relacionesCrudas = is_string($expense->relacion) ? json_decode($expense->relacion, true) : $expense->relacion;

                        foreach ($relacionesCrudas as $rel) {
                            switch ($rel['tabla']) {
                                case 1:
                                    $relacion = Invoice::find($rel['id']);
                                    $tabla = 'Factura';
                                    break;
                                case 2:
                                    $relacion = Ingreso::find($rel['id']);
                                    $tabla = 'Ingreso';
                                    break;
                                case 3:
                                    $relacion = Gasto::find($rel['id']);
                                    $tabla = 'Gasto';
                                    break;
                                case 4:
                                    $relacion = AssociatedExpenses::find($rel['id']);
                                    $tabla = 'Gasto asociado';
                                    break;
                                default:
                                    $relacion = null;
                                    $tabla = null;
                            }

                            if ($relacion) {
                                $relacionesMapeadas[] = [
                                    'modelo' => $relacion,
                                    'tabla' => $tabla,
                                    'id' => $expense->id,
                                ];
                            }
                        }
                    }

                    $expense->relaciones = $relacionesMapeadas;
                }
                $banks = BankAccounts::all();
                $allBudgets = Budget::whereIn('budget_status_id', [3, 6, 7, 9])->get();

                return view('dashboards.dashboard_gestor', compact('user', 'tareas', 'to_dos', 'budgets', 'projects', 'clientes', 'users', 'events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva', 'llamadaActiva', 'to_dos_finalizados', 'horasSemanales', 'unclassifiedIncomes', 'unclassifiedExpenses', 'banks', 'allBudgets'));

            case 4:
                $clientes = Client::where('is_client', true)->get();
                $budgets = Budget::where('admin_user_id', $id)->get();
                $projects = Project::where('admin_user_id', $id)->get();
                $tareas = Task::where('gestor_id', $id)->get();
                $v1 = count(Budget::where('admin_user_id', 2)->whereYear('created_at', 2202)->get()) / 12;
                $horasSemanales = $this->horasSemanales();

                return view('dashboards.dashboard_gestor', compact('user', 'tareas', 'to_dos', 'budgets', 'projects', 'clientes', 'users', 'events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva', 'llamadaActiva', 'to_dos_finalizados', 'horasSemanales'));
            case 5:
                $tareas = $user->tareas->whereIn('task_status_id', [1, 2, 5]);
                $tiempoProducidoHoy = $this->tiempoProducidoHoy();
                $tasks = $this->getTasks($user->id);

                $tareasFinalizadas = Task::where('admin_user_id', $user->id)
                    ->where('task_status_id', 3)
                    ->whereMonth('updated_at', Carbon::now()->month)
                    ->whereYear('updated_at', Carbon::now()->year)
                    ->whereRaw('TIME_TO_SEC(real_time) > 1740') // 29 minutos en segundos
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

                // Save or update monthly productivity with month and year
                $currentMonth = Carbon::now()->month;
                $currentYear = Carbon::now()->year;

                $productividadMensual = ProductividadMensual::where('admin_user_id', $user->id)->where('mes', $currentMonth)->where('año', $currentYear)->first();

                if (!$productividadMensual) {
                    ProductividadMensual::create([
                        'admin_user_id' => $user->id,
                        'mes' => $currentMonth,
                        'año' => $currentYear,
                        'productividad' => $totalProductividad,
                    ]);
                } else {
                    // Actualizar el registro existente
                    $productividadMensual->update([
                        'productividad' => $totalProductividad,
                    ]);
                }

                $productividadIndividual = $totalTareas > 0 ? $totalProductividad : 0;
                $horasMes = $this->tiempoProducidoMes($user->id);

                $data = $this->nota($user->id);
                $nota = $data['puntuacion'];
                $bajas = $data['bajas'];
                $horasSemanales = $this->horasSemanales();

                return view('dashboards.dashboard_personal', compact('user', 'tiempoProducidoHoy', 'tasks', 'tareas', 'to_dos', 'users', 'events', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva', 'productividadIndividual', 'totalEstimatedTime', 'totalRealTime', 'horasMes', 'to_dos_finalizados', 'nota', 'bajas', 'horasSemanales'));
            case 6:
                $ayudas = KitDigital::where('comercial_id', $user->id)->get();
                $fechaEmision = Carbon::now();
                $fechaExpiracion = new Carbon('last day of this month');
                $diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);
                $pedienteCierre = 0;
                $comisionCurso = 0;
                $comisionPendiente = 0;
                $comisionTramitadas = 0;
                $comisionRestante = 0;
                $estadosKit = KitDigitalEstados::all();

                foreach ($ayudas as $key => $ayuda) {
                    if ($ayuda->estado == 18 || $ayuda->estado == 17 || $ayuda->estado == 24) {
                        $pedienteCierre += $this->convertToNumber($ayuda->importe) * 0.05;
                    } elseif ($ayuda->estado == 10) {
                        $comisionCurso += $this->convertToNumber($ayuda->importe) * 0.05;
                    } elseif ($ayuda->estado == 4 || $ayuda->estado == 7 || $ayuda->estado == 5 || $ayuda->estado == 8 || $ayuda->estado == 9) {
                        $comisionPendiente += $this->convertToNumber($ayuda->importe) * 0.05;
                    } elseif ($ayuda->estado == 2) {
                        $comisionTramitadas += $this->convertToNumber($ayuda->importe) * 0.05;
                    } elseif ($ayuda->estado == 25) {
                        $comisionRestante += $this->convertToNumber($ayuda->importe) * 0.05;
                    }
                }
                return view('dashboards.dashboard_comercial', compact('user', 'diasDiferencia', 'estadosKit', 'comisionRestante', 'ayudas', 'comisionTramitadas', 'comisionPendiente', 'comisionCurso', 'pedienteCierre', 'timeWorkedToday', 'jornadaActiva', 'pausaActiva'));
        }
    }

    public function nota($userId)
    {
        $productividad = $this->productividadMesAnterior($userId);
        $horasMes = $this->tiempoProducidoMesanterior($userId);
        $partes = explode(':', $horasMes);
        $horas = $partes[0];
        $minutos = $partes[1];
        $segundos = $partes[2];
        $totalHorasproducidas = $horas + $minutos / 60 + $segundos / 3600;

        $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfMonth = Carbon::now()->subMonth()->endOfMonth();
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        $diasLaborables = $period->filter(function (Carbon $date) {
            return !$date->isWeekend(); // Retorna true si NO es sábado ni domingo
        });

        $diasReales = $diasLaborables->count();
        $vacaciones = $this->vacaciones($startOfMonth, $endOfMonth, $userId);
        $bajas = $this->bajas($userId, $startOfMonth, $endOfMonth);
        $festivos = $this->festivos($startOfMonth, $endOfMonth, $diasLaborables);

        $diasTotales = $diasReales - $vacaciones - $bajas - $festivos;
        $horasTotales = $diasTotales * 7;
        if ($totalHorasproducidas >= $horasTotales * 0.5) {
            $putuacionProductividad = $productividad / 20;
        } else {
            $putuacionProductividad = 0;
        }
        $putuacionHoras = ($totalHorasproducidas * 100) / $horasTotales / 20;

        //dd($productividad,$putuacionProductividad, $putuacionHoras);

        $putuacion = $putuacionProductividad + $putuacionHoras;

        $data = [
            'puntuacion' => $putuacion,
            'bajas' => $bajas,
        ];
        return $data;
    }

    public function productividadMesAnterior($id)
    {
        $month = Carbon::now()->subMonth();
        $year = $month->year;

        $tareasFinalizadas = Task::where('admin_user_id', $id)
            ->where('task_status_id', 3)
            ->whereMonth('updated_at', $month)
            ->whereYear('updated_at', $year)
            ->whereRaw('TIME_TO_SEC(real_time) > 1740') // 29 minutos en segundos
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

    public function vacaciones($ini, $fin, $id)
    {
        $vacaciones = HolidaysPetitions::where('admin_user_id', $id)->whereDate('from', '>=', $ini)->whereDate('to', '<=', $fin)->where('holidays_status_id', 1)->get();

        $dias = $vacaciones->sum('total_days');

        return $dias;
    }

    public function bajas($id, $ini, $fin)
    {
        $diasTotales = 0;

        // Obtener las bajas del usuario dentro del rango especificado
        $bajas = Baja::where('admin_user_id', $id)
            ->where(function ($query) use ($ini, $fin) {
                $query
                    ->whereBetween('inicio', [$ini, $fin])
                    ->orWhereBetween('fin', [$ini, $fin])
                    ->orWhere(function ($query) use ($ini, $fin) {
                        $query->where('inicio', '<=', $ini)->where('fin', '>=', $fin);
                    });
            })
            ->get();

        foreach ($bajas as $baja) {
            $inicioBaja = Carbon::parse($baja->inicio);
            $finBaja = Carbon::parse($baja->fin) ?? Carbon::now();

            // Ajustar fechas al intervalo especificado
            $fechaInicio = $inicioBaja->greaterThan($ini) ? $inicioBaja : Carbon::parse($ini);
            $fechaFin = $finBaja->lessThan($fin) ? $finBaja : Carbon::parse($fin);

            // Crear un período para las fechas ajustadas
            $period = CarbonPeriod::create($fechaInicio, $fechaFin);

            // Contar solo los días laborables en el período
            $diasLaborables = $period
                ->filter(function (Carbon $date) {
                    return !$date->isWeekend(); // Excluir sábados y domingos
                })
                ->count();

            $diasTotales += $diasLaborables;
        }

        return $diasTotales;
    }

    function getWorkingDaysInMonthUntilToday($year, $month)
    {
        // Obtener el día actual
        $currentDay = date('j');
        $totalDays = 0;

        // Iterar sobre los días del mes hasta el día actual
        for ($day = 1; $day <= $currentDay; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $weekday = date('N', strtotime($date)); // 1 (lunes) a 7 (domingo)

            // Contar solo si es un día laborable (lunes a viernes)
            if ($weekday >= 1 && $weekday <= 5) {
                $totalDays++;
            }
        }

        return $totalDays;
    }
    public function parseFlexibleTime($time)
    {
        [$hours, $minutes, $seconds] = explode(':', $time);
        return $hours * 60 + $minutes + $seconds / 60; // Convert to total minutes
    }

    public function tiempoProducidoMes($id)
    {
        $mes = Carbon::now();
        $tiempoTotalMes = 0;

        // Obtener todas las tareas del usuario en el mes actual
        $tareasMes = LogTasks::where('admin_user_id', $id)->whereYear('date_start', $mes->year)->whereMonth('date_start', $mes->month)->get();

        foreach ($tareasMes as $tarea) {
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
        $horas_mes_porcentaje = $hours + $minutes / 60;
        $porcentaje = ($horas_mes_porcentaje / $totalHorasMensuales) * 100;

        $data = [
            'horas' => $result,
            'porcentaje' => $porcentaje,
        ];

        return $result;
    }

    public function tiempoProducidoMesanterior($id)
    {
        $mes = Carbon::now()->subMonth()->startOfMonth();
        $tiempoTotalMes = 0;

        // Obtener todas las tareas del usuario en el mes actual
        $tareasMes = LogTasks::where('admin_user_id', $id)->whereYear('date_start', $mes->year)->whereMonth('date_start', $mes->month)->get();

        foreach ($tareasMes as $tarea) {
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
        $horas_mes_porcentaje = $hours + $minutes / 60;
        $porcentaje = ($horas_mes_porcentaje / $totalHorasMensuales) * 100;

        $data = [
            'horas' => $result,
            'porcentaje' => $porcentaje,
        ];

        return $result;
    }

    public function tiempoProducidoHoy()
    {
        $hoy = Carbon::today();
        $tiempoTarea = 0;

        if (Auth::check()) {
            $userId = Auth::id();
            $tareasHoy = LogTasks::where('admin_user_id', $userId)->whereDate('date_start', '=', $hoy)->get();

            foreach ($tareasHoy as $tarea) {
                if ($tarea->status == 'Pausada') {
                    $tiempoInicio = Carbon::parse($tarea->date_start);
                    $tiempoFinal = Carbon::parse($tarea->date_end);
                    $tiempoTarea += $tiempoFinal->diffInSeconds($tiempoInicio);
                }
            }
        } else {
            $result = '00:00:00';
        }

        // Formatear el tiempo total en horas, minutos y segundos
        $hours = floor($tiempoTarea / 3600);
        $minutes = floor(($tiempoTarea % 3600) / 60);
        $seconds = $tiempoTarea % 60;

        $result = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        // Calcular el porcentaje de tiempo trabajado en relación con el total
        $horas_dia_porcentaje = $hours + $minutes / 60;
        $totalHoras = 7;
        $porcentaje = ($horas_dia_porcentaje / $totalHoras) * 100;

        $data = [
            'horas' => $result,
            'porcentaje' => $porcentaje,
        ];

        return $data;
    }

    public function timeworked()
    {
        $user = Auth::user();
        $timeWorkedToday = $this->calculateTimeWorkedToday($user);
        return response()->json(['success' => true, 'time' => $timeWorkedToday]);
    }

    public function llamada(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate(
            [
                'client_id' => 'nullable',
                'phone' => 'nullable',
                'comentario' => 'nullable',
                'kit_id' => 'nullable',
            ],
            [
                'client_id.required_without' => 'El campo cliente es obligatorio si el teléfono no está presente.',
                'phone.required_without' => 'El campo teléfono es obligatorio si el cliente no está presente.',
            ],
        );
        $llamadaactiva = Llamada::where('admin_user_id', $user->id)->where('is_active', true)->first();

        if ($llamadaactiva) {
            $llamadaactiva->update([
                'end_time' => Carbon::now(),
                'is_active' => false,
            ]);
        }

        $llamada = Llamada::create([
            'admin_user_id' => $user->id,
            'start_time' => Carbon::now(),
            'is_active' => true,
            'client_id' => $data['client_id'] ?? null,
            'kit_id' => $data['kit_id'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
        return response()->json(['success' => true, 'mensaje' => 'Llamada iniciada']);
    }

    public function finalizar(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'comentario' => 'nullable',
        ]);
        $llamada = Llamada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($llamada) {
            $finllamada = $llamada->update([
                'end_time' => Carbon::now(),
                'is_active' => false,
                'comentario' => $data['comentario'] ?? null,
            ]);

            if (isset($data['comentario']) && $llamada->kit_id != null) {
                $kit = KitDigital::find($llamada->kit_id);
                $kit->comentario = $data['comentario'];
                $kit->fecha_actualizacion = Carbon::now();
                $kit->save();
            }

            return redirect()
                ->back()
                ->with('toast', [
                    'icon' => 'success',
                    'mensaje' => 'Llamada Finalizada',
                ]);
        }
    }

    public function startJornada()
    {
        $user = User::find(Auth::user()->id);

        $activeJornada = $user->activeJornada();

        if ($activeJornada) {
            // Si ya hay una jornada activa, retornar un mensaje indicando que no se puede iniciar otra
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una jornada activa.',
            ]);
        }

        $jornada = Jornada::create([
            'admin_user_id' => $user->id,
            'start_time' => Carbon::now(),
            'is_active' => true,
        ]);

        $todayJornada = Jornada::where('admin_user_id', $user->id)->whereDate('start_time', Carbon::today())->get();

        //Alertas de puntualidad
        if (count($todayJornada) == 1) {
            $horaLimiteEntrada = Carbon::createFromTime(9, 30, 0, 'Europe/Madrid');
            $horaLimiteEntradaUTC = $horaLimiteEntrada->setTimezone('UTC');
            $mesActual = Carbon::now()->month;
            $añoActual = Carbon::now()->year;
            $fechaActual = Carbon::now();

            $tardehoy = Jornada::where('admin_user_id', $user->id)->whereDate('start_time', $fechaActual->toDateString())->whereTime('start_time', '>', $horaLimiteEntradaUTC->format('H:i:s'))->get();

            $hourlyAverage = Jornada::where('admin_user_id', $user->id)
                ->whereMonth('start_time', $mesActual)
                ->whereYear('start_time', $añoActual)
                ->whereRaw('TIME(start_time) > ?', [$horaLimiteEntradaUTC->format('H:i:s')])
                ->get();

            $fechaNow = Carbon::now();

            if (count($tardehoy) > 0) {
                //Si hay mas de 3 veces
                if (count($hourlyAverage) > 2) {
                    $alertados = [1, 8];
                    foreach ($alertados as $alertar) {
                        $data = [
                            'admin_user_id' => $alertar,
                            'stage_id' => 23,
                            'description' => $user->name . ' ha llegado tarde 3 veces o mas este mes',
                            'status_id' => 1,
                            'reference_id' => $user->id,
                            'activation_datetime' => Carbon::now()->format('Y-m-d H:i:s'),
                        ];

                        $alert = Alert::create($data);
                        $alertSaved = $alert->save();
                    }
                }

                switch (count($hourlyAverage)) {
                    case 1:
                        $text = 'Hemos notado que hoy llegaste después de la hora límite de entrada (09:30). Entendemos que a veces pueden surgir imprevistos, pero te recordamos la importancia de respetar el horario para mantener la eficiencia en el equipo.';
                        break;
                    case 2:
                        $text = 'Nuevamente has llegado después de la hora límite de entrada (09:30). Reforzamos la importancia de cumplir con el horario para asegurar un buen rendimiento y organización en el equipo.';
                        break;
                    case 3:
                        $text = 'Se ha registrado tu llegada tarde tres veces. Esta información se compartirá con la Dirección. Es importante respetar los horarios para mantener el rendimiento y la organización del equipo.';
                        break;
                    default:
                        $text = 'Se ha registrado tu llegada tarde mas de  tres veces. Esta información se compartirá con la Dirección. Es importante respetar los horarios para mantener el rendimiento y la organización del equipo.';
                        break;
                }

                $data = [
                    'admin_user_id' => $user->id,
                    'stage_id' => 23,
                    'description' => $text,
                    'status_id' => 1,
                    'reference_id' => $user->id,
                    'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
                ];

                $alert = Alert::create($data);
                $alertSaved = $alert->save();
            }
        }

        if ($jornada) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'mensaje' => 'Error al iniciar jornada']);
        }
    }

    public function endJornada()
    {
        $user = Auth::user();
        $jornada = Jornada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($jornada) {
            $finJornada = $jornada->update([
                'end_time' => Carbon::now(),
                'is_active' => false,
            ]);
            $pause = Pause::where('jornada_id', $jornada->id)->whereNull('end_time')->first();
            if ($pause) {
                $finPause = $pause->update([
                    'end_time' => Carbon::now(),
                    'is_active' => false,
                ]);
            }
            if ($finJornada) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'mensaje' => 'Error al iniciar jornada']);
            }
        } else {
            return response()->json(['success' => false, 'mensaje' => 'Error al iniciar jornada']);
        }
    }

    public function startPause()
    {
        $user = Auth::user();
        $jornada = Jornada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($jornada) {
            $pause = Pause::create([
                'jornada_id' => $jornada->id,
                'start_time' => Carbon::now(),
            ]);

            if ($pause) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'mensaje' => 'Error al iniciar jornada']);
            }
        } else {
            return response()->json(['success' => false, 'mensaje' => 'Error al iniciar jornada']);
        }
    }

    public function endPause()
    {
        $user = Auth::user();
        $jornada = Jornada::where('admin_user_id', $user->id)->where('is_active', true)->first();
        if ($jornada) {
            $pause = Pause::where('jornada_id', $jornada->id)->whereNull('end_time')->first();
            if ($pause) {
                $finPause = $pause->update([
                    'end_time' => Carbon::now(),
                    'is_active' => false,
                ]);

                if ($finPause) {
                    return response()->json(['success' => true]);
                } else {
                    return response()->json(['success' => false, 'mensaje' => 'Error al iniciar jornada']);
                }
            } else {
                return response()->json(['success' => false, 'mensaje' => 'Error al iniciar jornada']);
            }
        } else {
            return response()->json(['success' => false, 'mensaje' => 'Error al iniciar jornada']);
        }
    }

    private function calculateTimeWorkedToday($user)
    {
        $todayJornadas = $user->jornadas()->whereDate('start_time', Carbon::today())->get();

        $totalWorkedSeconds = 0;

        foreach ($todayJornadas as $jornada) {
            $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? Carbon::now());
            $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
                return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? Carbon::now());
            });
            $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
        }

        return $totalWorkedSeconds;
    }

    public function getTasks($id)
    {
        $tasks = [];
        $tasksPause = Task::where('admin_user_id', $id)->where('task_status_id', 2)->orderBy('priority_id', 'desc')->get();
        $tasks['tasksPause'] = $tasksPause;
        $tasksRevision = Task::where('admin_user_id', $id)->where('task_status_id', 5)->get();
        $tasks['tasksRevision'] = $tasksRevision;
        $taskPlay = Task::where('admin_user_id', $id)->where('task_status_id', 1)->get()->first();
        $tasks['taskPlay'] = $taskPlay;

        return $tasks;
    }

    public function getDataTask(Request $request)
    {
        $tarea = Task::find($request->id);
        //$metas = DB::table('meta')->where("tasks_id", $request->id)->get();
        $autor = $tarea->usuario;
        if ($tarea) {
            $data = [];
            $data['id'] = $tarea->id;
            $data['user'] = $tarea->admin_user_id;
            $data['titulo'] = $tarea->title;
            $data['cliente'] = Optional(Optional($tarea->presupuesto)->cliente)->name ?? 'Cliente no encontrado';
            $data['descripcion'] = $tarea->description;
            $data['estimado'] = $tarea->estimated_time;
            $data['real'] = $tarea->real_time;
            $data['proyecto'] = Optional($tarea->proyecto)->name ?? 'Proyecto no encontrado';
            $data['prioridad'] = Optional($tarea->prioridad)->name ?? 'Prioridad no encontrada';
            $data['gestor'] = $tarea->gestor->name;
            $data['gestorid'] = Optional($tarea->gestor)->id ?? 'Gestor no encontrado';
            $data['estado'] = $tarea->estado->name;
            $data['metas'] = '';
            $data['userName'] = $autor;

            $response = json_encode($data);

            return $response;
        } else {
            $response = json_encode([
                'estado' => 'ERROR',
            ]);

            return $response;
        }
    }

    public function getTasksRefresh()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $usuario = User::find($userId);
            $tareas = $this->getTasks($usuario->id);

            return $tareas;
        }
    }

    public function setStatusTask(Request $request)
    {
        $tarea = Task::find($request->id);
        $date = Carbon::now();
        $userId = Auth::id();
        $usuario = User::find($userId);

        $formatEstimated = strtotime($tarea->estimated_time);
        $formatReal = strtotime($tarea->real_time);

        $clientIP = request()->ip();

        $error = false;

        //if($clientIP == "81.45.82.225" || $usuario->access_level_id == 4 || $usuario->access_level_id == 3){

        if ($tarea) {
            switch ($request->estado) {
                case 'Reanudar':
                    $tareaActiva = Task::where('admin_user_id', $usuario->id)->where('task_status_id', 1)->get()->first();

                    if (!$tareaActiva) {
                        $tarea->task_status_id = 1;
                    }

                    $logTaskC = DB::select("SELECT id FROM `log_tasks` WHERE `status` = 'Reanudada' AND `admin_user_id` = $usuario->id");
                    if (count($logTaskC) == 1) {
                        $error = true;
                    } else {
                        $createLog = LogTasks::create([
                            'admin_user_id' => $usuario->id,
                            'task_id' => $tarea->id,
                            'date_start' => $date,
                            'date_end' => null,
                            'status' => 'Reanudada',
                        ]);

                        if ($tarea->real_time > $tarea->estimated_time) {
                            // Calcular el porcentaje de exceso

                            [$realHours, $realMinutes, $realSeconds] = explode(':', $tarea->real_time);
                            $realTimeInSeconds = $realHours * 3600 + $realMinutes * 60 + $realSeconds;

                            [$estimatedHours, $estimatedMinutes, $estimatedSeconds] = explode(':', $tarea->estimated_time);
                            $estimatedTimeInSeconds = $estimatedHours * 3600 + $estimatedMinutes * 60 + $estimatedSeconds;

                            // Calcular el porcentaje de exceso basado en segundos
                            $exceedPercentage = ($realTimeInSeconds / $estimatedTimeInSeconds) * 100;

                            // Inicializar datos comunes de la alerta
                            $data = [
                                'admin_user_id' => $tarea->gestor_id,
                                'status_id' => 1,
                                'reference_id' => $tarea->id,
                                'activation_datetime' => Carbon::now(),
                            ];

                            // Definir el mensaje y el stage_id según el porcentaje de exceso
                            if ($exceedPercentage >= 100) {
                                $data['stage_id'] = 40; // Stage para el 100% de sobrepaso
                                $data['description'] = 'Tarea ' . $tarea->id . ' ' . $tarea->title . ' ha sobrepasado las horas estimadas en un 100% o más (pérdidas)';
                            } elseif ($exceedPercentage >= 50) {
                                $data['stage_id'] = 40; // Stage para el 50% de sobrepaso
                                $data['description'] = 'Tarea ' . $tarea->id . ' ' . $tarea->title . ' está sobrepasando las horas estimadas en un 50%';
                            } else {
                                $data['stage_id'] = 40; // Stage para sobrepaso menor al 50%
                                $data['description'] = 'Aviso de Tarea - Se está sobrepasando las horas estimadas en la tarea ' . $tarea->title;
                            }

                            $existe = Alert::where('status_id', 1)->where('stage_id', $data['stage_id'])->where('reference_id', $tarea->id)->where('description', $data['description'])->exists();
                            // Crear y guardar la alerta
                            if (!$existe) {
                                $alert = Alert::create($data);
                                $alertSaved = $alert->save();

                                $data['admin_user_id'] = 1;
                                $alertAdmin = Alert::create($data);
                                $alertSaved = $alertAdmin->save();
                            }
                        }

                        $logTask = DB::select("SELECT id FROM `log_tasks` WHERE date_start BETWEEN DATE_SUB(now(), interval 6 hour) AND DATE_ADD(NOW(), INTERVAL 7 hour) AND `admin_user_id` = $usuario->id");
                        if (count(value: $logTask) == 1) {
                            $activeJornada = $usuario->activeJornada();

                            if (!$activeJornada) {
                                $jornada = Jornada::create([
                                    'admin_user_id' => $usuario->id,
                                    'start_time' => Carbon::now(),
                                    'is_active' => true,
                                ]);
                            }

                            $horaLimiteEntrada = Carbon::createFromTime(9, 30, 0, 'Europe/Madrid');
                            $horaLimiteEntradaUTC = $horaLimiteEntrada->setTimezone('UTC');
                            $mesActual = Carbon::now()->month;
                            $añoActual = Carbon::now()->year;
                            $fechaActual = Carbon::now();

                            $todayJornada = Jornada::where('admin_user_id', $usuario->id)->whereDate('start_time', $fechaActual->toDateString())->whereTime('start_time', '>', $horaLimiteEntradaUTC->format('H:i:s'))->get();

                            $hourlyAverage = Jornada::where('admin_user_id', $usuario->id)
                                ->whereMonth('start_time', $mesActual)
                                ->whereYear('start_time', $añoActual)
                                ->whereRaw('TIME(start_time) > ?', [$horaLimiteEntradaUTC->format('H:i:s')])
                                ->get();

                            $fechaNow = Carbon::now();

                            if (count($todayJornada) > 0) {
                                if (count($hourlyAverage) > 2) {
                                    $alertados = [1, 8];
                                    foreach ($alertados as $alertar) {
                                        $data = [
                                            'admin_user_id' => $alertar,
                                            'stage_id' => 23,
                                            'description' => $usuario->name . ' ha llegado tarde 3 veces o mas este mes',
                                            'status_id' => 1,
                                            'reference_id' => $usuario->id,
                                            'activation_datetime' => Carbon::now()->format('Y-m-d H:i:s'),
                                        ];

                                        $alert = Alert::create($data);
                                        $alertSaved = $alert->save();
                                    }
                                }

                                switch (count($hourlyAverage)) {
                                    case 1:
                                        $text = 'Hemos notado que hoy llegaste después de la hora límite de entrada (09:30). Entendemos que a veces pueden surgir imprevistos, pero te recordamos la importancia de respetar el horario para mantener la eficiencia en el equipo.';
                                        break;
                                    case 2:
                                        $text = 'Nuevamente has llegado después de la hora límite de entrada (09:30). Reforzamos la importancia de cumplir con el horario para asegurar un buen rendimiento y organización en el equipo.';
                                        break;
                                    case 3:
                                        $text = 'Se ha registrado tu llegada tarde tres veces. Esta información se compartirá con la Dirección. Es importante respetar los horarios para mantener el rendimiento y la organización del equipo.';
                                        break;
                                    default:
                                        $text = 'Se ha registrado tu llegada tarde mas de  tres veces. Esta información se compartirá con la Dirección. Es importante respetar los horarios para mantener el rendimiento y la organización del equipo.';
                                        break;
                                }

                                $data = [
                                    'admin_user_id' => $usuario->id,
                                    'stage_id' => 23,
                                    'description' => $text,
                                    'status_id' => 1,
                                    'reference_id' => $usuario->id,
                                    'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
                                ];

                                $alert = Alert::create($data);
                                $alertSaved = $alert->save();
                            }
                        }
                    }
                    break;
                case 'Pausada':
                    if ($tarea->task_status_id == 1) {
                        if ($tarea->real_time == '00:00:00') {
                            $start = $tarea->updated_at;
                            $end = new \DateTime('NOW');
                            $interval = $end->diff($start);

                            $time = sprintf('%02d:%02d:%02d', $interval->d * 24 + $interval->h, $interval->i, $interval->s);
                        } else {
                            $start = $tarea->updated_at;
                            $end = new \DateTime('NOW');
                            $interval = $end->diff($start);

                            $time = sprintf('%02d:%02d:%02d', $interval->d * 24 + $interval->h, $interval->i, $interval->s);

                            $time = $this->sum_the_time($tarea->real_time, $time);
                        }
                        $tarea->real_time = $time;
                    }

                    $last = LogTasks::where('admin_user_id', $usuario->id)->get()->last();
                    if ($last) {
                        $last->date_end = $date;
                        $last->status = 'Pausada';
                        $last->save();
                    }

                    $tarea->task_status_id = 2;
                    break;
                case 'Revision':
                    //Crear Alerta tarea terminada antes de tiempo
                    // if ($formatEstimated > $formatReal) {
                    //     $dataAlert = [
                    //         'admin_user_id' => $usuario->id,
                    //         'stage_id' => 14,
                    //         'activation_datetime' => $date->format('Y-m-d H:i:s'),
                    //         'status_id' => 1,
                    //         'reference_id' => $tarea->id,
                    //     ];

                    //     $alert = Alert::create($dataAlert);
                    //     $alertSaved = $alert->save();
                    // }

                    $dataAlert = [
                        'admin_user_id' => $tarea->gestor_id,
                        'stage_id' => 41,
                        'activation_datetime' => $date->format('Y-m-d H:i:s'),
                        'status_id' => 1,
                        'reference_id' => $tarea->id,
                    ];

                    $alert = Alert::create($dataAlert);
                    $alertSaved = $alert->save();

                    $tarea->task_status_id = 5;
                    break;
            }

            $taskSaved = $tarea->save();

            if (($taskSaved || $tareaActiva == null) && !$error) {
                $response = json_encode([
                    'estado' => 'OK',
                ]);
            } else {
                $response = json_encode([
                    'estado' => 'ERROR; TIENES OTRA TAREA ACTIVA. HABLA CON EL CREADOR .`,',
                ]);
            }
        } else {
            $response = json_encode([
                'estado' => 'ERROR',
            ]);
        }
        //}

        return $response;
    }

    function sum_the_time($time1, $time2)
    {
        $times = [$time1, $time2];
        $seconds = 0;
        foreach ($times as $time) {
            [$hour, $minute, $second] = explode(':', $time);
            $seconds += $hour * 3600;
            $seconds += $minute * 60;
            $seconds += $second;
        }
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        // return "{$hours}:{$minutes}:{$seconds}";
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    function convertToNumber($importe)
    {
        // Elimina los puntos de separación de miles
        $importe = str_replace('.', '', $importe);
        // Reemplaza la coma decimal por un punto decimal
        $importe = str_replace(',', '.', $importe);
        // Convierte a número flotante
        return (float) $importe;
    }

    public function updateStatusAlertAndAcceptHours(Request $request)
    {
        $alert = Alert::find($request->id);
        $hoursMonthly = HoursMonthly::find($alert->reference_id);
        $hoursMonthly->acceptance_hours = 'CONFORME';
        $hoursMonthly->save();
        $alert->status_id = $request->status;
        $alertSaved = $alert->save();

        if ($alertSaved) {
            $response = json_encode([
                'estado' => '200',
            ]);
        } else {
            $response = 503;
        }

        return $response;
    }

    public function responseAlert(Request $request)
    {
        $alert = Alert::find($request->id);
        if ($alert->stage_id == 22) {
            $alert->description = $request->texto;
            $alert->save();
        }
        $note = Note::find($alert->reference_id);
        if ($note) {
            $user = $note->admin_user_id;
        } else {
            $user = 1;
        }
        $text = $request->texto;
        Carbon::setLocale('es');
        $fechaNow = Carbon::now();
        $data = [
            'admin_user_id' => $user,
            'stage_id' => 19,
            'description' => $text,
            'status_id' => AlertStatus::ALERT_STATUS_PENDING,
            'reference_id' => Auth::user()->id,
            'activation_datetime' => $fechaNow->format('Y-m-d H:i:s'),
        ];

        $alertCreate = Alert::create($data);
        $alertSaved = $alertCreate->save();

        if ($alertSaved) {
            if ($note) {
                $note->content = $note->content . " \nRespuesta: " . $text;
                $note->save();
            }
            return 200;
        } else {
            return 503;
        }
    }

    public function puntualidad($ini, $fin, $id)
    {
        $puntualidad = Alert::where('admin_user_id', $id)->whereDate('created_at', '>=', $ini)->whereDate('created_at', '<=', $fin)->where('stage_id', 23)->whereRaw('admin_user_id = reference_id')->get();

        $dias = $puntualidad->count();

        return $dias;
    }

    public function productividad($ini, $fin, $id)
    {
        $tareasFinalizadas = Task::where('admin_user_id', $id)
            ->where('task_status_id', 3)
            ->whereDate('updated_at', '>=', $ini)
            ->whereDate('updated_at', '<=', $fin)
            ->whereRaw('TIME_TO_SEC(real_time) > 1740') // 29 minutos en segundos
            ->get();

        $totalProductividad = 0;
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
        return number_format($totalProductividad, 2, ',', '.');
    }

    public function horasTrabajadasEnRango($fechaInicio, $fechaFin, $id)
    {
        $totalWorkedSeconds = 0;

        $jornadas = Jornada::where('admin_user_id', $id)->whereDate('start_time', '>=', $fechaInicio)->whereDate('start_time', '<=', $fechaFin)->get();

        foreach ($jornadas as $jornada) {
            if ($jornada->end_time) {
                $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time);
            } else {
                $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds(Carbon::now());
            }
            $totalPauseSeconds = 0;
            if ($jornada->pauses) {
                foreach ($jornada->pauses as $pause) {
                    $totalPauseSeconds += Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? Carbon::now());
                }
            }
            $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
        }
        // Convertir segundos a formato hh:mm:ss
        $horas = floor($totalWorkedSeconds / 3600);
        $minutos = floor(($totalWorkedSeconds % 3600) / 60);
        // Formatear la salida para asegurar siempre dos dígitos
        $tiempoFormateado = sprintf('%02d h %02d m', $horas, $minutos);

        return $tiempoFormateado;
    }

    public function tiempoProducidoEnRango($fechaInicio, $fechaFin, $id)
    {
        $tiempoTarea = 0;
        // Filtrar las tareas que estén dentro del rango de fechas
        $tareas = LogTasks::where('admin_user_id', $id)->whereDate('date_start', '>=', $fechaInicio)->whereDate('date_start', '<=', $fechaFin)->get();
        // Recorrer todas las tareas dentro del rango de fechas
        foreach ($tareas as $tarea) {
            if ($tarea->status == 'Pausada' && $tarea->date_end) {
                $tiempoInicio = Carbon::parse($tarea->date_start);
                $tiempoFinal = Carbon::parse($tarea->date_end);
                $tiempoTarea += $tiempoInicio->diffInMinutes($tiempoFinal);
            }
        }
        // Convertir minutos a formato hh:mm:ss
        $horas = floor($tiempoTarea / 60);
        $minutos = $tiempoTarea % 60;
        // Formatear la salida para asegurar siempre dos dígitos
        $tiempoFormateado = sprintf('%02d h %02d m', $horas, $minutos);

        return $tiempoFormateado;
    }

    public function presupuestosCreados($fechaInicio, $fechaFin, $id)
    {
        $presupuestos = Budget::where('admin_user_id', $id)->whereDate('created_at', '>=', $fechaInicio)->whereDate('created_at', '<=', $fechaFin)->get();

        return $presupuestos->count();
    }

    public function facturasCreados($fechaInicio, $fechaFin, $id)
    {
        $facturas = Invoice::where('admin_user_id', $id)->whereDate('created_at', '>=', $fechaInicio)->whereDate('created_at', '<=', $fechaFin)->get();

        return $facturas->count();
    }

    public function llamadas($fechaInicio, $fechaFin, $id)
    {
        $llamadas = Llamada::where('admin_user_id', $id)->whereDate('created_at', '>=', $fechaInicio)->whereDate('created_at', '<=', $fechaFin)->get();

        return $llamadas->count();
    }

    public function peticiones($fechaInicio, $fechaFin, $id)
    {
        $peticiones = Petition::where('admin_user_id', $id)->whereDate('created_at', '>=', $fechaInicio)->whereDate('created_at', '<=', $fechaFin)->where('finished', 1)->get();

        return $peticiones->count();
    }

    public function peticionesCreadas($fechaInicio, $fechaFin, $id)
    {
        $peticiones = Petition::where('admin_user_id', $id)->whereDate('created_at', '>=', $fechaInicio)->whereDate('created_at', '<=', $fechaFin)->get();

        return $peticiones->count();
    }

    public function gestionkit($fechaInicio, $fechaFin, $id)
    {
        $logActions = LogActions::where('tipo', 1)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('admin_user_id', $id)
            ->get();

        $referenceIdsUniquePerDay = $logActions
            ->groupBy(function ($action) {
                // Agrupar por la fecha
                return Carbon::parse($action->created_at)->format('Y-m-d');
            })
            ->map(function ($group) {
                // Para cada grupo, pluck y unique los referenceIds
                return $group->pluck('reference_id')->unique();
            });

        $totalCounts = $referenceIdsUniquePerDay->map(function ($ids) {
            // Contar el número de reference_ids únicos en cada subcolección
            return $ids->count();
        });

        // Si quieres el total global de todos los días
        $globalTotal = $totalCounts->sum();

        return $globalTotal;
    }

    public function kitsCreados($fechaInicio, $fechaFin, $id)
    {
        $logActions = LogActions::where('tipo', 1)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('admin_user_id', $id)
            ->where('action', 'like', '%Crear kit digital%')
            ->get();

        return $logActions->count();
    }

    public function produccion($fechaInicio, $fechaFin)
    {
        $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        $user = User::where('inactive', 0)->where('access_level_id', 5)->get();
        $data = [];
        foreach ($user as $usuario) {
            $data[] = [
                'nombre' => $usuario->name,
                'inpuntualidad' => $this->puntualidad($fechaInicio, $fechaFin, $usuario->id),
                'horas_oficinas' => $this->horasTrabajadasEnRango($fechaInicio, $fechaFin, $usuario->id),
                'horas_producidas' => $this->tiempoProducidoEnRango($fechaInicio, $fechaFin, $usuario->id),
                'productividad' => $this->productividad($fechaInicio, $fechaFin, $usuario->id),
            ];
        }
        return $data;
    }

    public function gestion($fechaInicio, $fechaFin)
    {
        $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        $user = User::where('inactive', 0)->where('access_level_id', 4)->get();
        $data = [];
        foreach ($user as $usuario) {
            $data[] = [
                'nombre' => $usuario->name,
                'inpuntualidad' => $this->puntualidad($fechaInicio, $fechaFin, $usuario->id),
                'horas_oficinas' => $this->horasTrabajadasEnRango($fechaInicio, $fechaFin, $usuario->id),
                'presu_generados' => $this->presupuestosCreados($fechaInicio, $fechaFin, $usuario->id),
                'llamadas' => $this->llamadas($fechaInicio, $fechaFin, $usuario->id),
                'kits' => $this->gestionkit($fechaInicio, $fechaFin, $usuario->id),
                'kitsCreados' => $this->kitsCreados($fechaInicio, $fechaFin, $usuario->id),
                'peticiones' => $this->peticiones($fechaInicio, $fechaFin, $usuario->id),
            ];
        }
        return $data;
    }

    public function contabilidad($fechaInicio, $fechaFin)
    {
        $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        $user = User::where('inactive', 0)->where('access_level_id', 3)->get();
        $data = [];
        foreach ($user as $usuario) {
            $data[] = [
                'nombre' => $usuario->name,
                'inpuntualidad' => $this->puntualidad($fechaInicio, $fechaFin, $usuario->id),
                'horas_oficinas' => $this->horasTrabajadasEnRango($fechaInicio, $fechaFin, $usuario->id),
                'facturas' => $this->facturasCreados($fechaInicio, $fechaFin, $usuario->id),
                'llamadas' => $this->llamadas($fechaInicio, $fechaFin, $usuario->id),
            ];
        }
        return $data;
    }

    public function comercial($fechaInicio, $fechaFin)
    {
        $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        $user = User::where('inactive', 0)->where('access_level_id', 6)->get();
        $data = [];
        foreach ($user as $usuario) {
            $data[] = [
                'nombre' => $usuario->name,
                'horas_oficinas' => $this->horasTrabajadasEnRango($fechaInicio, $fechaFin, $usuario->id),
                'kits_creados' => $this->kitsCreados($fechaInicio, $fechaFin, $usuario->id),
                'peticiones' => $this->peticionesCreadas($fechaInicio, $fechaFin, $usuario->id),
            ];
        }
        return $data;
    }

    public function getProduccion(Request $request)
    {
        $fechas = explode(' a ', $request->input('dateRange', now()->startOfMonth()->format('Y-m-d')));
        $fechaInicio = Carbon::parse($fechas[0]);
        $fechaFin = Carbon::parse($fechas[1]);
        $produccion = $this->produccion($fechaInicio, $fechaFin);
        return $produccion;
    }

    public function getGestion(Request $request)
    {
        $fechas = explode(' a ', $request->input('dateRange', now()->startOfMonth()->format('Y-m-d')));
        $fechaInicio = Carbon::parse($fechas[0]);
        $fechaFin = Carbon::parse($fechas[1]);
        $gestion = $this->gestion($fechaInicio, $fechaFin);
        return $gestion;
    }
    public function getComercial(Request $request)
    {
        $fechas = explode(' a ', $request->input('dateRange', now()->startOfMonth()->format('Y-m-d')));
        $fechaInicio = Carbon::parse($fechas[0]);
        $fechaFin = Carbon::parse($fechas[1]);
        $comercial = $this->comercial($fechaInicio, $fechaFin);
        return $comercial;
    }
    public function getContabilidad(Request $request)
    {
        $fechas = explode(' a ', $request->input('dateRange', now()->startOfMonth()->format('Y-m-d')));
        $fechaInicio = Carbon::parse($fechas[0]);
        $fechaFin = Carbon::parse($fechas[1]);
        $contabilidad = $this->contabilidad($fechaInicio, $fechaFin);
        return $contabilidad;
    }

    public function getKitDigital()
    {
        try {
            $kits = kitDigital::with('servicios') // Asegúrate de especificar los campos que necesitas de la relación
                ->get(['id', 'cliente', 'servicio_id']); // Asegúrate de que los campos aquí sean correctos

            return response()->json([
                'success' => true,
                'kits' => $kits,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al obtener los datos: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function horasSemanales()
    {
        $user = User::find(Auth::user()->id);

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->addDays(5);

        if ($user->access_level_id == 5) {
            $lastTask = LogTasks::where('admin_user_id', $user->id)->latest()->first();
        }

        if (isset($lastTask)) {
            $ultimatarea = Carbon::parse($lastTask->date_end ?? Carbon::now());
        } else {
            $ultimatarea = Carbon::now();
        }

        $jornadas = $user
            ->jornadas()
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->get();

        // Calcular tiempo trabajado por día
        $descontar = 0;

        $jornadasPorDia = $jornadas->groupBy(function ($jornada) {
            return Carbon::parse($jornada->start_time)->format('Y-m-d'); // Agrupar por día
        });

        foreach ($jornadasPorDia as $day => $dayJornadas) {
            $totalWorkedSeconds = 0;
            $isFriday = Carbon::parse($day)->isFriday();
            $isHalfDay = HolidaysPetitions::where('admin_user_id', $user->id)->where('holidays_status_id', 1)->where('from', '<=', $day)->where('to', '>=', $day)->first();
            foreach ($dayJornadas as $jornada) {
                $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? $ultimatarea);
                $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
                    return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? $pause->start_time);
                });
                $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
            }

            // Calcular la diferencia: 7 horas si es viernes, 8 horas en el resto de días
            if ($isHalfDay) {
                $targetHours = 5;
            } else {
                $targetHours = $isFriday ? 7 : 8;
            }
            $targetseconds = $targetHours * 3600;
            $difference = $targetseconds - $totalWorkedSeconds;

            if ($difference > 0) {
                // El usuario trabajó menos de las horas objetivo, debe compensar
                $descontar += $difference;
            } elseif ($difference < 0) {
                $descontar += $difference;
            }
        }
        //dd($descontar);
        $hours = floor($descontar / 3600);
        $minutes = floor(($descontar % 3600) / 60);
        $seconds = $descontar % 60;
        if ($descontar > 0) {
            $result = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            $result = '00:00:00';
        }

        return $result;
    }

    public function informeLlamadas(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate(
            [
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required',
                'admin_user_ids' => 'nullable|array',
                'admin_user_ids.*' => 'exists:admin_user,id',
            ],
            [
                'fecha_inicio.required' => 'El campo Fecha Inicio es obligatorio.',
                'fecha_fin.required' => 'El campo Fecha Fin es obligatorio.',
            ],
        );

        $url = url('/llamadas?selectedGestor=' . $user->id . '&fecha_inicio=' . $data['fecha_inicio'] . '&fecha_fin=' . $data['fecha_fin']);

        $validatedData['titulo'] = 'Informe de llamadas';
        $validatedData['url'] = $url;
        $validatedData['admin_user_id'] = $user->id;
        $validatedData['admin_user_ids'] = $data['admin_user_ids'];
        $validatedData['finalizada'] = false;

        $todo = Todo::create($validatedData);

        TodoUsers::create([
            'todo_id' => $todo->id,
            'admin_user_id' => $validatedData['admin_user_id'],
            'completada' => false, // Asumimos que la tarea no está completada por los usuarios al inicio
        ]);
        // Asociar múltiples usuarios a la tarea
        if (isset($validatedData['admin_user_ids'])) {
            foreach ($validatedData['admin_user_ids'] as $userId) {
                TodoUsers::create([
                    'todo_id' => $todo->id,
                    'admin_user_id' => $userId,
                    'completada' => false, // Asumimos que la tarea no está completada por los usuarios al inicio
                ]);
            }
        }
        $users = $todo->TodoUsers
            ->pluck('admin_user_id') // Obtén todos los admin_user_id
            ->reject(function ($adminUserId) use ($todo) {
                return $adminUserId == $todo->admin_user_id; // Excluye el admin_user_id del remitente
            });

        foreach ($users as $user) {
            $data = [
                'admin_user_id' => $user,
                'stage_id' => 44,
                'activation_datetime' => Carbon::now(),
                'status_id' => 1,
                'reference_id' => $todo->id,
                'description' => 'Nuevo To-Do con titulo : ' . $todo->titulo,
            ];
            $alert = Alert::create($data);
        }
    }
}
