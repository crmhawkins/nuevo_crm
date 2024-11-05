<?php

namespace App\Http\Controllers\Petitions;

use App\Http\Controllers\Controller;
use App\Models\Petitions\Petition;
use App\Models\Clients\Client;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\Projects\Project;
use App\Models\Todo\Todo;
use App\Models\Todo\TodoUsers;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $petitions = Petition::all();
        return view('petitions.index', compact('petitions'));
    }
    public function indexUser()
    {
        $petitions = Petition::where('admin_user_id',Auth()->user()->id)->get();
        return view('petitions.indexUser', compact('petitions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        session('clienteId') != null ? $clienteId = session('clienteId') : $clienteId = null;

        if(isset($clienteId)){
            $gestorId = Client::find($clienteId)->gestor->id;

        }else{
            $gestorId = null;
        }

        $gestores = User::where('inactive',0)->whereIn('access_level_id',[4,3])->get();
        $clientes = Client::where('is_client',true)->orderBy('id', 'asc')->get();


        return view('petitions.create', compact('gestores', 'clientes', 'clienteId','gestorId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){

        if ($request['client_id'] == 0){
            $dataClient = $this->validate($request, [
                'name' => 'required|max:200',
                'admin_user_id' => 'required|exists:admin_user,id',
                'email' => 'required|email:filter',
                'phone' => 'required',
            ], [
                'name.required' => 'El nombre es requerido para continuar',
                'admin_user_id.required' => 'El gestor es requerido para continuar',
                'admin_user_id.exists' => 'El gestor debe ser valido para continuar',
                'email.required' => 'El email es requerido para continuar',
                'email.email' => 'El email debe ser un email valido',
                'phone.required' => 'El telefono es requerido para continuar',
            ]);
            $clienteCreado = Client::create($dataClient);
            $request['client_id'] = $clienteCreado->id;
        }

        // Validamos los campos
        $data = $this->validate($request, [
            'client_id' => 'required|integer',
            'admin_user_id' => 'required|integer',
            'note' => 'required|max:255',
        ], [
            'client_id.required' => 'El cliente es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'note.required' => 'El concepto es requerido para continuar',
        ]);

        $data['finished '] = 0;

        $petitionCreado = Petition::create($data);
        $todoCreado = Todo::create([
            'titulo' => 'Peticion de '.Client::find( $data['client_id'])->name,
            'descripcion' =>  $data['note'],
            'admin_user_id' => $request['admin_user_id'],
        ]);
        TodoUsers::create([
            'todo_id' => $todoCreado->id,
            'admin_user_id' => $request['admin_user_id'],
            'completada' => false  // Asumimos que la tarea no está completada por los usuarios al inicio
        ]);

        return redirect(route('peticion.indexUser'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $peticion = Petition::find($id);
        $gestores = User::where('inactive',0)->whereIn('access_level_id',[4,3])->get();
        $clientes = Client::orderBy('id', 'asc')->get();
        if (!$peticion) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'La petición no existe'
            ]);
            return redirect()->route('peticion.index');
        }

        return view('petitions.edit', compact('peticion',  'gestores', 'clientes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $peticion = Petition::find($id);
        // Validación
        $data = $this->validate($request, [
            'client_id' => 'required|integer',
            'admin_user_id' => 'required|integer',
            'note' => 'required|max:255',
        ], [
            'client_id.required' => 'El cliente es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'note.required' => 'El concepto es requerido para continuar',
        ]);

        $petitionCreado = $peticion->update($data);
        return redirect(route('peticion.indexUser'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        if ($id != null) {
            $peticion = Petition::find($id);

            if ($peticion != null) {

                // Eliminar el presupuesto
                $peticion->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "La petición fue borrada con éxito."
                ]);

            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra la petición."
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

}
