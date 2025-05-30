<?php

namespace App\Http\Controllers\To_do;

use App\Http\Controllers\Controller;
use App\Models\Alerts\Alert;
use App\Models\Events\Event;
use App\Models\Todo\Todo;
use App\Models\Todo\TodoUsers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class To_doController extends Controller
{
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'task_id' => 'nullable|exists:tasks,id',
            'client_id' => 'nullable|exists:clients,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'project_id' => 'nullable|exists:projects,id',
            'admin_user_ids' => 'nullable|array',
            'admin_user_ids.*' => 'exists:admin_user,id',
            'admin_user_id' =>'required',
            'url' => 'nullable',
        ]);

        $validatedData['finalizada'] = false;
        $todo = Todo::create($validatedData);

        TodoUsers::create([
            'todo_id' => $todo->id,
            'admin_user_id' => $validatedData['admin_user_id'],
            'completada' => false  // Asumimos que la tarea no está completada por los usuarios al inicio
        ]);

        // Asociar múltiples usuarios a la tarea
        if(isset($validatedData['admin_user_ids'])){
            foreach ($validatedData['admin_user_ids'] as $userId) {
                TodoUsers::create([
                    'todo_id' => $todo->id,
                    'admin_user_id' => $userId,
                    'completada' => false  // Asumimos que la tarea no está completada por los usuarios al inicio
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
                'description' => 'Nuevo To-Do con titulo : '.$todo->titulo,

            ];
            $alert = Alert::create($data);
        }

        if($request->agendar == true){

            foreach($todo->TodoUsers as $user){
                $data = $this->validate($request, [
                    'descripcion' => 'nullable',
                    'client_id' => 'nullable',
                    'budget_id' => 'nullable',
                    'project_id' => 'nullable',
                    'color' => 'nullable',
                    'start' => 'nullable',
                    'end' => 'nullable',
                ]);
                $data['title'] = $request->titulo;
                $data['admin_user_id'] = $user->admin_user_id;

                Event::create($data);

            }
       }



        return redirect()->back()->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Tarea creada exitosamente!'
        ]);
    }
    public function finish($id)
    {
        $user = auth()->user();
        $todo = Todo::find($id);
        if($todo->admin_user_id = $user->id){
            $todoupdated = $todo->update([ 'finalizada' => true]);
            if($todoupdated){
                return response()->json([
                    'success' =>true
                ]);
            }else{
                return response()->json([
                    'success' =>false
                ]);
            }
        }else{
            return response()->json([
                'success' =>false
            ]);
        }

    }

    public function complete($id)
    {
        $user = auth()->user();
        $todo = Todo::find($id);
        $todouser = $todo->TodoUsers->where('admin_user_id',$user->id)->first();
        $todouserupdated = $todouser->update([ 'completada' => true]);
        if ($todouserupdated) {
            $data = [
                'admin_user_id' => $todo->admin_user_id,
                'stage_id' => 46,
                'activation_datetime' => Carbon::now(),
                'status_id' => 1,
                'reference_id' => $todo->id,
                'description' => 'El to-do con titulo "'.$todo->titulo.'" ha sido completado por '.$user->name,
            ];
            $alert = Alert::create($data);

            return response()->json([
                'success' =>true
            ]);
        }else{
            return response()->json([
                'success' =>false
            ]);
        }
    }
    public function getUnreadMessagesCount($todoId){
        $userId = auth()->user()->id;
        $toDo = Todo::find($todoId);

        if (!$toDo) {
            return response()->json(['unreadCount' => 0], 404);
        }

        $unreadCount = $toDo->unreadMessagesCountByUser($userId);

        return response()->json(['unreadCount' => $unreadCount]);
    }

}
