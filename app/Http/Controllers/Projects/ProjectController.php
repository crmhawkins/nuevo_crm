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
        return redirect()->route('campania.show', $proyectoCreado->id);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createFromBudget(string $id)
    {
        $cliente = Client::find($id);
        return view('campania.createFromBudget', compact('cliente'));
    }
    public function storeFromBudget(Request $request)
    {
        // Validamos los campos
        // $this->validate($request, [
        //     'client_id' => 'required|integer',
        //     'name' => 'required|max:200',
        //     'description' => 'required|max:200',
        // ], [
        //     'client_id.required' => 'El cliente es requerido para continuar',
        //     'name.required' => 'El nombre de la campaña es requerido para continuar',
        //     'description.required' => 'La descripción de la campaña es requerido para continuar',
        // ]);

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El cliente se creo correctamente'
        ]);
        $protectId = 0;
        $clienteId = $request->client_id;
        // dd($clienteId);
        return redirect(route('presupuesto.create'))->with('clienteId', $clienteId, 'projectId', $protectId);
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
}
