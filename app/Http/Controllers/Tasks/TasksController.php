<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Jornada\Jornada;
use App\Models\Prioritys\Priority;
use App\Models\Tasks\LogTasks;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskStatus;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{
    public function index()
    {
        $tareas = Task::all();
        return view('tasks.index', compact('tareas'));
    }
    public function cola()
    {
        $usuarios = User::where('access_level_id',5)
            ->where('inactive', 0)
            ->whereHas('departamento', function($query) {
                $query->where('name', '!=', 'Construccion');
            })
            ->get();
        //$usuarios = User::all();
        return view('tasks.cola', compact('usuarios'));
    }
    public function revision()
    {
        $tareas = Task::all();
        return view('tasks.revision', compact('tareas'));
    }
    public function asignar()
    {
        $tareas = Task::all();
        return view('tasks.asignar', compact('tareas'));
    }

    public function edit(string $id)
    {
        $task = Task::find($id);

        if(!isset($task)){
            return redirect()->route('tareas.index')->with('toast',[
                'icon' => 'success',
                'mensaje' => 'Tarea no encontrada'
            ]);
        }

        $employees = User::where('inactive', 0)->get();
        $prioritys = Priority::all();
        $status = TaskStatus::all();
        $data = [];

                // Calcular el tiempo total ya asignado (usar real_time si está disponible, sino estimated_time)
        $totalAssignedTime = 0;
        $totalConsumedTime = 0; // Suma de todas las horas reales
        $existingTasks = Task::where('split_master_task_id', $task->id)->get();
        foreach ($existingTasks as $existingTask) {
            // Usar el máximo entre estimado y real, o real si está disponible
            $estimatedSeconds = time_to_seconds($existingTask->estimated_time);
            $realSeconds = time_to_seconds($existingTask->real_time ?? '00:00:00');
            // Usar el mayor entre estimado y real para considerar tareas sobrepasadas
            $totalAssignedTime += max($estimatedSeconds, $realSeconds);
            // Sumar solo las horas reales para el tiempo consumido
            $totalConsumedTime += $realSeconds;
        }
        
        // Usar total_time_budget como presupuesto para tareas maestras
        $totalBudgetTime = time_to_seconds($task->total_time_budget ?? $task->estimated_time);
        $timeExceeded = $totalAssignedTime > $totalBudgetTime;

        if ($task->duplicated == 0) {
            $trabajador = User::find($task->admin_user_id);
            if ($trabajador) {
                $data = [
                    '0' => [
                        'num' => 1,
                        'id' => $trabajador->id,
                        'trabajador' => $trabajador->name,
                        'horas_estimadas' => $task->estimated_time,
                        'horas_reales' => $task->real_time,
                        'status' => $task->task_status_id,
                        'task_id' => $task->id,
                    ],
                ];
            }
        } else {
            $count = 1;
            $tareasDuplicadas = Task::where(
                'split_master_task_id',
                $task->id
            )->get();
            $trabajador = User::find($task->admin_user_id);

            if ($trabajador) {
                $data = [
                    '0' => [
                        'num' => 1,
                        'id' => $trabajador->id,
                        'trabajador' => $trabajador->name,
                        'horas_estimadas' => $task->estimated_time,
                        'horas_reales' => $task->real_time,
                        'status' => $task->task_status_id,
                        'task_id' => $task->id,
                    ],
                ];
            } else {
                $count = 0;
            }

            foreach ($tareasDuplicadas as $tarea) {
                // Incluir TODAS las subtareas, incluso si no tienen admin_user_id
                $data[$count]['num'] = $count + 1;
                $data[$count]['task_id'] = $tarea->id;
                $data[$count]['horas_estimadas'] = $tarea->estimated_time;
                $data[$count]['horas_reales'] = $tarea->real_time;
                $data[$count]['status'] = $tarea->task_status_id;
                
                if ($tarea->admin_user_id) {
                    $trabajador = User::find($tarea->admin_user_id);
                    if ($trabajador == null) {
                        $data[$count]['id'] = 1;
                        $data[$count]['trabajador'] = 'No existe';
                    } else {
                        $data[$count]['id'] = $trabajador->id;
                        $data[$count]['trabajador'] = $trabajador->name;
                    }
                } else {
                    // Si no tiene admin_user_id, poner valores por defecto
                    $data[$count]['id'] = null;
                    $data[$count]['trabajador'] = 'Sin asignar';
                }
                
                $count++;
            }
        }
        return view('tasks.edit', compact('task', 'prioritys', 'employees', 'data', 'status', 'timeExceeded', 'totalAssignedTime', 'totalBudgetTime', 'totalConsumedTime'));
    }

    public function update(Request $request)
    {
        $loadTask = Task::find($request->taskId);

        // Si la tarea es subtarea, solo guardar cambios y salir
        if ($loadTask->split_master_task_id != null) {
            $loadTask->admin_user_id = $request['employeeId1'] ?? $loadTask->admin_user_id;
            $loadTask->estimated_time = $request['estimatedTime1'] ?? $loadTask->estimated_time;
            $loadTask->real_time = $request['realTime1'] ?? $loadTask->real_time;
            $loadTask->priority_id = $request['priority'] ?? $loadTask->priority_id;
            $loadTask->task_status_id = $request['status1'] ?? $loadTask->task_status_id;
            $loadTask->title = $request['title'] ?? $loadTask->title;
            $loadTask->description = $request['description'] ?? $loadTask->description;
            $loadTask->save();
            return redirect()->route('tarea.edit',$loadTask->id)->with('toast',[
                'icon' => 'success',
                'mensaje' => 'Tarea actualizada'
            ]);
        }

        // Si la tarea es maestra (no tiene split_master_task_id)
        $esMaestra = is_null($loadTask->split_master_task_id);

        // Verificar si hay subtareas que se intentan eliminar y tienen horas reales
        if ($esMaestra) {
            $existingSubtasks = Task::where('split_master_task_id', $loadTask->id)->get();
            $subtasksInRequest = [];
            
            // Recolectar IDs de subtareas que están en el request (todos los taskId presentes)
            // Recorrer todos los posibles índices desde 1 hasta numEmployee
            for ($i = 1; $i <= $request['numEmployee']; $i++) {
                $taskId = $request['taskId' . $i] ?? null;
                // Solo añadir si existe, no es "temp" y es numérico (ID válido)
                if ($taskId && $taskId != 'temp' && is_numeric($taskId)) {
                    $subtasksInRequest[] = (int)$taskId;
                }
            }
            
            // Verificar subtareas que existen pero no están en el request (fueron eliminadas)
            foreach ($existingSubtasks as $subtask) {
                if (!in_array($subtask->id, $subtasksInRequest)) {
                    // Esta subtarea fue eliminada, verificar si tiene horas reales
                    $realSeconds = time_to_seconds($subtask->real_time ?? '00:00:00');
                    if ($realSeconds > 0) {
                        return redirect()->back()->withErrors([
                            'delete_error' => 'No se puede eliminar la subtarea "' . $subtask->title . '" porque tiene horas reales consumidas (' . $subtask->real_time . ').'
                        ])->withInput();
                    }
                }
            }
        }

        // Guardar cambios en subtareas si corresponde
        for ($i = 1; $i <= $request['numEmployee']; $i++) {
            $taskId = $request['taskId' . $i] ?? null;
            
            // Si taskId es "temp" o no es numérico, es una nueva subtarea
            if ($taskId && $taskId != 'temp' && is_numeric($taskId)) {
                // Actualizar subtarea existente
                $exist = Task::find($taskId);
                if ($exist) {
                    $exist->admin_user_id = $request['employeeId' . $i];
                    $exist->estimated_time = $request['estimatedTime' . $i];
                    $exist->real_time = $request['realTime' . $i];
                    $exist->priority_id = $request['priority'];
                    $exist->task_status_id = $request['status' . $i];
                    $exist->save();
                }
            } else {
                // Crear nueva subtarea (taskId es "temp" o no existe)
                // Solo crear si hay un empleado seleccionado
                if (!empty($request['employeeId' . $i])) {
                    $data = [];
                    $data['admin_user_id'] = $request['employeeId' . $i];
                    $data['gestor_id'] = $loadTask->gestor_id;
                    $data['priority_id'] = $request['priority'] ?? $loadTask->priority_id;
                    $data['project_id'] = $loadTask->project_id;
                    $data['budget_id'] = $loadTask->budget_id;
                    $data['budget_concept_id'] = $loadTask->budget_concept_id;
                    $data['task_status_id'] = $request['status' . $i] ?? 2;
                    $data['split_master_task_id'] = $loadTask->id;
                    $data['duplicated'] = 0;
                    $data['description'] = $request['description'] ?? $loadTask->description;
                    $data['title'] = $request['title'] ?? $loadTask->title;
                    $data['estimated_time'] = $request['estimatedTime' . $i] ?? '00:00:00';
                    $data['real_time'] = $request['realTime' . $i] ?? '00:00:00';
                    
                    // Validar campos requeridos antes de crear
                    if (empty($data['title'])) {
                        $data['title'] = $loadTask->title;
                    }
                    if (empty($data['project_id'])) {
                        \Log::error('Error: project_id es null al crear subtarea');
                    }
                    if (empty($data['budget_id'])) {
                        \Log::error('Error: budget_id es null al crear subtarea');
                    }
                    
                    try {
                        $newtask = Task::create($data);
                        \Log::info('Subtarea creada exitosamente', ['id' => $newtask->id, 'title' => $newtask->title]);
                    } catch (\Exception $e) {
                        \Log::error('Error al crear subtarea: ' . $e->getMessage());
                        \Log::error('Datos intentados: ' . json_encode($data));
                        \Log::error('Stack trace: ' . $e->getTraceAsString());
                        return redirect()->back()->withErrors([
                            'create_error' => 'Error al crear la subtarea: ' . $e->getMessage()
                        ])->withInput();
                    }
                }
            }
        }

        // Eliminar subtareas que no están en el request y no tienen horas reales
        if ($esMaestra) {
            $existingSubtasks = Task::where('split_master_task_id', $loadTask->id)->get();
            $subtasksInRequest = [];
            
            // Recolectar IDs de subtareas que están en el request
            for ($i = 1; $i <= $request['numEmployee']; $i++) {
                $taskId = $request['taskId' . $i];
                if ($taskId && $taskId != 'temp') {
                    $subtasksInRequest[] = $taskId;
                }
            }
            
            // Eliminar subtareas que no están en el request y no tienen horas reales
            foreach ($existingSubtasks as $subtask) {
                if (!in_array($subtask->id, $subtasksInRequest)) {
                    $realSeconds = time_to_seconds($subtask->real_time ?? '00:00:00');
                    // Solo eliminar si no tiene horas reales (la validación anterior ya previno las que tienen)
                    if ($realSeconds == 0) {
                        $subtask->delete();
                    }
                }
            }
        }

        // Guardar cambios generales
        $loadTask->title = $request['title'];
        $loadTask->description = $request['description'];
        $loadTask->priority_id = $request['priority'];
        $loadTask->duplicated = 1;
        $loadTask->save();

        // Si es maestra, actualizar el real_time sumando los real_time de las subtareas
        if ($esMaestra) {
            $subtareas = Task::where('split_master_task_id', $loadTask->id)->get();
            $totalRealTime = 0;
            foreach ($subtareas as $sub) {
                $totalRealTime += time_to_seconds($sub->real_time);
            }
            $loadTask->real_time = seconds_to_time($totalRealTime);
            $loadTask->save();

            // Validar tiempo DESPUÉS de crear/actualizar todas las subtareas (solo advertencia, no bloqueo)
            $totalTimeSubtareas = 0;
            foreach ($subtareas as $sub) {
                // Usar el máximo entre estimado y real para considerar tareas sobrepasadas
                $estimatedSeconds = time_to_seconds($sub->estimated_time);
                $realSeconds = time_to_seconds($sub->real_time ?? '00:00:00');
                $totalTimeSubtareas += max($estimatedSeconds, $realSeconds);
            }
            // Si la suma de los tiempos de las subtareas supera el presupuesto de la maestra, advertencia (no bloqueo)
            $budgetTime = $loadTask->total_time_budget ?? $loadTask->estimated_time;
            if ($totalTimeSubtareas > time_to_seconds($budgetTime)) {
                // Solo mostrar advertencia en la sesión, pero permitir continuar
                return redirect()->route('tarea.edit',$loadTask->id)->with('toast',[
                    'icon' => 'success',
                    'mensaje' => 'Tarea actualizada'
                ])->with('warning', 'El tiempo total asignado (' . seconds_to_time($totalTimeSubtareas) . ') supera el presupuesto de la tarea maestra (' . $budgetTime . ').');
            }
        }

        return redirect()->route('tarea.edit',$loadTask->id)->with('toast',[
            'icon' => 'success',
            'mensaje' => 'Tarea actualizada'
        ]);
    }

        /**
     * Convierte tiempo en formato HH:MM:SS a segundos
     */
    private function timeToSeconds($time)
    {
        return time_to_seconds($time);
    }

    /**
     * Convierte segundos a formato HH:MM:SS
     */
    public function secondsToTime($seconds)
    {
        return seconds_to_time($seconds);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'task_status_id' => 'required|exists:task_status,id',
        ]);

        $task = Task::findOrFail($id);
        $task->task_status_id = $request->task_status_id;
        $task->save();

        return back()->with('success', 'Estado de la tarea actualizado correctamente.');
    }

    public function calendar($id)
    {
        $user = User::where('id', $id)->first();

        // Obtener los eventos de tareas para el usuario
        $events = $this->getLogTasks($id);
        // Convertir los eventos en formato adecuado para FullCalendar (si no están ya en ese formato)
        $eventData = [];
        foreach ($events as $event) {

            $inicio = Carbon::createFromFormat('Y-m-d H:i:s', $event[1], 'UTC');
            $inicioEspaña = $inicio->setTimezone('Europe/Madrid');
            if(isset($event[2])){
                $fin = Carbon::createFromFormat('Y-m-d H:i:s', $event[2], 'UTC');
                $finEspaña = $fin->setTimezone('Europe/Madrid');
            }

            $eventData[] = [
                'id' => $event[3],
                'title' => $event[0],
                'start' => $inicioEspaña->toIso8601String(), // Aquí debería estar la fecha y hora de inicio
                'end' => $event[2] ? $finEspaña->toIso8601String() : null , // Aquí debería estar la fecha y hora de fin
                'allDay' => false, // Indica si el evento es de todos los días
                'color' =>$event[4]
            ];
        }
        // Datos adicionales de horas trabajadas y producidas
        //$horas = $this->getHorasTrabajadas($user);
        $horas_hoy = $this->getHorasTrabajadasHoy($user);
        $horas_hoy2 = $this->getHorasTrabajadasHoy2($user);
        //$horas_dia = $this->getHorasTrabajadasDia($user);

        // Pasar los datos de eventos a la vista como JSON
        return view('tasks.timeLine', [
            'user' => $user,
            'horas_hoy' => $horas_hoy,
            'horas_hoy2' => $horas_hoy2,
            'events' => $eventData // Enviar los eventos como JSON
        ]);
    }


    public function getHorasTrabajadasDia($usuario)
    {
        $horasTrabajadas = DB::select("SELECT SUM(TIMESTAMPDIFF(MINUTE,date_start,date_end)) AS minutos FROM log_tasks where date_start >= cast(now() As Date) AND `admin_user_id` = $usuario->id");
        $hora = floor($horasTrabajadas[0]->minutos / 60);
        $minuto = ($horasTrabajadas[0]->minutos % 60);
        $horas_dia = $hora . ' Horas y ' . $minuto . ' minutos';

        return $horas_dia;
    }

    public function getHorasTrabajadas($usuario)
    {
        $horasTrabajadas = DB::select("SELECT SUM(TIMESTAMPDIFF(MINUTE,date_start,date_end)) AS minutos FROM `log_tasks` WHERE date_start BETWEEN now() - interval (day(now())-1) day AND LAST_DAY(NOW()) AND `admin_user_id` = $usuario->id");
        $hora = floor($horasTrabajadas[0]->minutos / 60);
        $minuto = ($horasTrabajadas[0]->minutos % 60);
        $horas = $hora . ' Horas y ' . $minuto . ' minutos';

        return $horas;
    }

    // Horas producidas hoy
    public function getHorasTrabajadasHoy($user)
    {
        // Se obtiene los datos
        $id = $user->id;
        $fecha = Carbon::now()->toDateString();;
        $resultado = 0;
        $totalMinutos2 = 0;

        $logsTasks = LogTasks::where('admin_user_id', $id)
        ->whereDate('date_start', '=', $fecha)
        ->get();

        foreach($logsTasks as $item){
            if($item->date_end == null){
                $item->date_end = Carbon::now();
            }
            $to_time2 = strtotime($item->date_start);
            $from_time2 = strtotime($item->date_end);
            $minutes2 = ($from_time2 - $to_time2) / 60;
            $totalMinutos2 += $minutes2;
        }

        $hora2 = floor($totalMinutos2 / 60);
        $minuto2 = ($totalMinutos2 % 60);
        $horas_dia2 = $hora2 . ' Horas y ' . $minuto2 . ' minutos';

        $resultado = $horas_dia2;

        return $resultado;
    }

    // Horas trabajadas hoy
    public function getHorasTrabajadasHoy2($user)
    {
         // Se obtiene los datos
         $id = $user->id;
         $fecha = Carbon::now()->toDateString();
         $hoy = Carbon::now();
         $resultado = 0;
         $totalMinutos2 = 0;


        $almuerzoHoras = 0;

        $jornadas = Jornada::where('admin_user_id', $id)
        ->whereDate('start_time', $hoy)
        ->get();

        $totalWorkedSeconds = 0;
        foreach($jornadas as $jornada){
            $workedSeconds = Carbon::parse($jornada->start_time)->diffInSeconds($jornada->end_time ?? Carbon::now());
            $totalPauseSeconds = $jornada->pauses->sum(function ($pause) {
                return Carbon::parse($pause->start_time)->diffInSeconds($pause->end_time ?? Carbon::now());
            });
            $totalWorkedSeconds += $workedSeconds - $totalPauseSeconds;
        }
        $horasTrabajadasFinal = $totalWorkedSeconds / 60;

        $hora = floor($horasTrabajadasFinal / 60);
        $minuto = ($horasTrabajadasFinal % 60);

        $horas_dia = $hora . ' Horas y ' . $minuto . ' minutos';

        return $horas_dia;
    }

    public function getLogTasks($idUsuario)
    {
        $events = [];
        $logs = LogTasks::where("admin_user_id", $idUsuario)->get();
        $end = Carbon::now()->format('Y-m-d H:i:s');
        $now = Carbon::now()->format('Y-m-d H:i:s');


        foreach ($logs as $index => $log) {

           $fin = $now;

           if ($log->date_end == null) {
                $nombre = isset($log->tarea->presupuesto->cliente->name) ? $log->tarea->presupuesto->cliente->name : 'El cliente no tiene nombre o no existe';

                $events[] =[
                    "Titulo: " . $log->tarea->title . "\n " . "Cliente: " . $nombre,
                    $log->date_start,
                    $fin,
                    $log->task_id,
                    '#FD994E'

                ];
            } else {
                $nombre = isset($log->tarea->presupuesto->cliente->name) ? $log->tarea->presupuesto->cliente->name : 'El cliente no tiene nombre o no existe';
                $events[] = [
                    "Titulo: " . $log->tarea->title . "\n " . "Cliente: " . $nombre,
                    $log->date_start,
                    $log->date_end,
                    $log->task_id,
                    '#FD994E'

                ];
            }
        }
        return $events;
    }

    public function destroy(Request $request)
    {
        $tarea = Task::find($request->id);

        if (!$tarea) {
            return response()->json([
                'status' => false,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $tarea->delete();
        return response()->json([
            'status' => true,
            'mensaje' => 'El tarea borrada correctamente'
        ]);
    }
}
