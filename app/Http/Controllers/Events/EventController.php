<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\Events\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function store(Request $request)
    {
        // Validamos los campos
        $data = $this->validate($request, [
            'title' => 'required|max:200',
            'descripcion' => 'nullable',
            'client_id' => 'nullable',
            'budget_id' => 'nullable',
            'project_id' => 'nullable',
            'color' => 'nullable',
            'admin_user_id' => 'required|exists:admin_user,id',
            'start' => 'required',
            'end' => 'nullable',
        ], [
            'title.required' => 'El nombre es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'admin_user_id.exists' => 'El gestor debe ser valido para continuar',
            'start.required' => 'El email es requerido para continuar',
        ]);
        $eventoCreado = Event::create($data);

        if($eventoCreado){
            return redirect()->back()->with(
              'toast', [
                'icon' => 'success',
                'mensaje' => 'El evento se creo correctamente'
            ]);
        }else{
            return redirect()->back()->with(
              'toast', [
                'icon' => 'error',
                'mensaje' => 'Error al crear el evento'
            ]);
        }
    }
}

