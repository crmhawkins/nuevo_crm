<?php

namespace App\Http\Controllers\To_do;

use App\Http\Controllers\Controller;
use App\Models\Todo\Todo;
use App\Models\Todo\TodoUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class To_doController extends Controller
{
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'task_id' => 'nullable|exists:tasks,id',
            'client_id' => 'nullable|exists:clients,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'project_id' => 'nullable|exists:projects,id',
            'admin_user_ids' => 'required|array',
            'admin_user_ids.*' => 'exists:admin_users,id',
            'admin_user_id' =>'required'
        ]);

        $todo = Todo::create([
            'titulo' => $validatedData['title'],
            'descripcion' => $validatedData['descripcion'],
            'task_id' => $validatedData['task_id'],
            'client_id' => $validatedData['client_id'],
            'budget_id' => $validatedData['budget_id'],
            'project_id' => $validatedData['project_id'],
            'admin_user_id' => $validatedData['admin_user_id'],
            'finalizada' => false  // Asumimos que la tarea no está finalizada al crearse
        ]);

        TodoUsers::create([
            'todo_id' => $todo->id,
            'admin_user_id' => $validatedData['admin_user_id'],
            'completada' => false  // Asumimos que la tarea no está completada por los usuarios al inicio
        ]);

        // Asociar múltiples usuarios a la tarea
        foreach ($validatedData['admin_user_ids'] as $userId) {
            TodoUsers::create([
                'todo_id' => $todo->id,
                'admin_user_id' => $userId,
                'completada' => false  // Asumimos que la tarea no está completada por los usuarios al inicio
            ]);
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
}
