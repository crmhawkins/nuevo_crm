<?php

namespace App\Http\Controllers\To_do;

use App\Http\Controllers\Controller;
use App\Models\Events\Event;
use App\Models\Todo\Todo;
use App\Models\Todo\TodoUsers;
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
            'admin_user_id' =>'required'
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
        if($todouserupdated){
            return response()->json([
                'success' =>true
            ]);
        }else{
            return response()->json([
                'success' =>false
            ]);
        }
    }

    public function getTodosForUser()
    {
        $user = auth()->user(); // Obtener el usuario autenticado
        $to_dos = $user->todos()->with(['proyecto', 'cliente', 'presupuesto', 'TodoUsers', 'mensajes'])
        ->get();

    // Formatear los To-Dos en un formato JSON que pueda ser utilizado en JavaScript
    return response()->json($to_dos);
    }
}
