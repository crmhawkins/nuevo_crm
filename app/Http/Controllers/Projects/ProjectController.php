<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Projects\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campanias = Project::all();
        return view('campania.index', compact('campanias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        session('clienteId') != null ? $clienteId = session('clienteId') : $clienteId = null;
        $clientes = Client::orderBy('id', 'asc')->get();


        return view('campania.create', compact('clientes', 'clienteId'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'client_id' => 'required|integer',
            'name' => 'required|max:200',
        ], [
            'client_id.required' => 'El cliente es requerido para continuar',
            'name.required' => 'La campaña es requerido para continuar',
        ]);

        $request['admin_user_id'] = auth()->user()->id;
        $proyectoCreado = Project::create($request->all());

        if ($proyectoCreado != null) {
            session()->flash('toast', [
                'icon' => 'success',
                'mensaje' => 'La campaña se creo correctamente'
            ]);
        } else {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'Ocurrio un error en el servidor, intentelo mas tarde'
            ]);
        }
        return redirect()->route('campania.edit', parameters: $proyectoCreado->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id){

        $project = Project::find($id);
        if (!$project) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'La campaña no existe'
            ]);
            return redirect()->route('campania.index');
        }
        // Obtener listas de opciones necesarias para el formulario
        $clientes = Client::orderBy('id', 'asc')->get();

        return view('campania.edit', compact('project', 'clientes'));

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){
        $project = Project::find($id);
        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'client_id' => 'required|integer',
            'name' => 'required|max:200',
            'description' => 'required|max:200',
            'notes' => 'nullable'
        ], [
            'client_id.required' => 'El cliente es requerido para continuar',
            'name.required' => 'El nombre de la campaña es requerido para continuar',
            'description.required' => 'La descripción de la campaña es requerido para continuar',
        ]);
        // Actualizar el gasto con los datos validados
        $project->update($validated);
        // Redireccionar con mensaje de éxito
        return redirect()->route('campania.index')->with('toast',[
            'icon' => 'success',
            'mensaje' => 'Campaña actualizada con exito'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request){
        $id = $request->id;
        if ($id != null) {
            $project = Project::find($id);
            if ($project != null) {
                // Eliminar el presupuesto
                $project->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "La campaña fue borrada con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra la Campaña."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createFromBudget(string $id)
    {
        $cliente = Client::find($id);
        $rutaPrevia = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();

        if($rutaPrevia == 'presupuesto.edit') {
        $presupuesto =  session()->get('presupuesto_id') ?? null;
        }else{
            session()->forget('presupuesto_id');
            $presupuesto =  null;
        }

        return view('campania.createFromBudget', compact('cliente', 'presupuesto'));
    }

    public function createFromBudgetAndPetition(string $id, string $petitionid)
    {
        $cliente = Client::find($id);

        return view('campania.createFromBudget', compact('cliente','petitionid'));
    }
    public function storeFromBudget(Request $request)
    {        // Validamos los campos
        session()->forget('presupuesto_id');
        $data = $this->validate($request, [
            'client_id' => 'required|integer',
            'name' => 'required|max:200',
            'description' => 'required|max:200',
            'notes' => 'nullable'
        ], [
            'client_id.required' => 'El cliente es requerido para continuar',
            'name.required' => 'El nombre de la campaña es requerido para continuar',
            'description.required' => 'La descripción de la campaña es requerido para continuar',
        ]);

        $data['admin_user_id'] = auth()->user()->id;

        $proyectoCreado = Project::create($data);
        $presupuesto  =  $request->presupuesto_id ?? null;
        $projectId = $proyectoCreado->id;
        $clienteId = $request->client_id;
        $petitionId = $request->petition_id;
        $cliente =  Client::find( $clienteId);

        if ($proyectoCreado != null) {
            session()->flash('toast', [
                'icon' => 'success',
                'mensaje' => 'La campaña se creo correctamente'
            ]);
        } else {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'Ocurrio un error en el servidor, intentelo mas tarde'
            ]);
        }
        if(isset($petitionId)){
            return redirect(route('presupuesto.createFromPetition', $petitionId))->with(['clienteId' => $clienteId,'projectId' => $projectId ]);
        }elseif(isset($presupuesto)){
            return redirect(route('presupuesto.edit', $presupuesto))->with(['clienteId' => $clienteId,'projectId' => $projectId ]);
        }else{
             return redirect(route('presupuesto.create'))->with(['clienteId' => $clienteId,'projectId' => $projectId ]);
        }
    }
    public function updateFromWindow(Request $request)
    {
        $campanias = Project::where('client_id', $request->input('client_id'))->get();
        return $campanias;
    }

    public function postProjectsFromClient(Request $request){
        $client = Client::find($request->input('client_id'));
        $campanias = Project::where('client_id', $client->id)->get();
        return response($campanias);
    }
    public function getProjectById(Request $request)
    {
        $project = Project::find($request->input('project_id'));
        return response($project);
    }

}
