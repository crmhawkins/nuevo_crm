<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Prioritys\Priority;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskStatus;
use App\Models\Users\User;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index()
    {
        $tareas = Task::all();
        return view('tasks.index', compact('tareas'));
    }
    public function cola()
    {
        // $usuarios = User::where('access_level_id ',5)->get();
        $usuarios = User::all();
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
        $employees = User::where('inactive', 0)->get();
        $prioritys = Priority::all();
        $status = TaskStatus::all();
        $data = [];
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
                if ($tarea->admin_user_id) {

                    $trabajador = User::find($tarea->admin_user_id);
                    if ($trabajador == null ) {
                        $data[$count]['num'] = $count + 1;
                        $data[$count]['id'] = 1 ;
                        $data[$count]['trabajador'] = 'No existe';
                        $data[$count]['horas_estimadas'] = $tarea->estimated_time;
                        $data[$count]['horas_reales'] = $tarea->real_time;
                        $data[$count]['status'] = $tarea->task_status_id;
                        $data[$count]['task_id'] = $tarea->id;
                        $count++;
                    } else {
                        $data[$count]['num'] = $count + 1;
                        $data[$count]['id'] = $trabajador->id ;
                        $data[$count]['trabajador'] = $trabajador->name;
                        $data[$count]['horas_estimadas'] = $tarea->estimated_time;
                        $data[$count]['horas_reales'] = $tarea->real_time;
                        $data[$count]['status'] = $tarea->task_status_id;
                        $data[$count]['task_id'] = $tarea->id;
                        $count++;
                    }
                }
            }
        }
        return view('tasks.edit', compact('task', 'prioritys', 'employees', 'data', 'status'));
    }

    public function update(Request $request)
    {
        $loadTask = Task::find($request->taskId);
        for ($i = 1; $i <= $request['numEmployee']; $i++) {
            $exist = Task::find($request['taskId' . $i]);
            if ($exist) {
                $exist->admin_user_id = $request['employeeId' . $i];
                $exist->estimated_time = $request['estimatedTime' . $i];
                $exist->real_time = $request['realTime' . $i];
                $exist->priority_id = $request['priority'];
                $exist->task_status_id = $request['status' . $i];

                $exist->save();
            } else {
                if ($request['employeeId' . $i]) {
                    $data['admin_user_id'] = $request['employeeId' . $i];
                    $data['gestor_id'] = $loadTask->gestor_id;
                    $data['priority_id'] = $request['priority'];
                    $data['project_id'] = $loadTask->project_id;
                    $data['budget_id'] = $loadTask->budget_id;
                    $data['budget_concept_id'] =
                        $loadTask->budget_concept_id;
                    $data['task_status_id'] = $request['status' . $i];
                    $data['split_master_task_id'] = $loadTask->id;
                    $data['duplicated'] = 0;
                    $data['description'] = $request['description'];
                    $data['title'] = $request['title'];
                    $data['estimated_time'] =
                        $request['estimatedTime' . $i];
                    $data['real_time'] = $request['realTime' . $i];

                    $newtask = Task::create($data);
                    $taskSaved = $newtask->save();
                }
            }
        }
        $loadTask->title = $request['title'];
        $loadTask->description = $request['description'];
        $loadTask->duplicated = 1;
        $loadTask->save();

        return redirect()->route('tarea.edit',$loadTask->id)->with('toast',[
            'icon' => 'success',
            'mensaje' => 'Tarea actualizada'
        ]);
    }

}
