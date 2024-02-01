<?php

namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\Projects\Project;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Budget::all();
        return view('budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        session('clienteId') != null ? $clienteId = session('clienteId') : $clienteId = null;

        $gestores = User::all();
        $clientes = Client::orderBy('id', 'asc')->get();
        // $campanias = Project::all();
        $formasPago = PaymentMethod::all();
        $campanias = [];

        return view('budgets.create', compact('gestores', 'clientes', 'campanias', 'formasPago', 'clienteId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'client_id' => 'required|integer',
            'projetc_id' => 'required|integer',
            'admin_user_id' => 'required|integer',
            'concept' => 'required|max:200',
        ], [
            'client_id.required' => 'El cliente es requerido para continuar',
            'projetc_id.required' => 'La campaña es requerido para continuar',
            'admin_user_id.required' => 'El gestor es requerido para continuar',
            'concept.required' => 'El concepto es requerido para continuar',
        ]);

        $budgetTemporal = Budget::where('temp', true)->orderBy('created_at', 'desc')->first();
        $referenceTemp = $budgetTemporal === null ? 'temp_00' : $this->generateReferenceTemp($budgetTemporal->reference);
        // $referenceTemp = $this->generateReferenceTemp(intval('temp_00'));

        dd($referenceTemp);
        $data = $request->all();
        $data['temp'] = true;
        $data['reference'] = $referenceTemp;
        $data['creation_date'] = Carbon::now();

        $budgetCreado = Budget::create($data);

    }

    private function generateReferenceTemp($numero){
        // Incrementa el número primero
        $incrementedNumber = intval($numero) + 1;
        // Asegura que el número tenga dos dígitos
        $formattedNumber = str_pad($incrementedNumber, 2, '0', STR_PAD_LEFT);
        // Concatena con la cadena "temp_"
        return "temp_" . $formattedNumber;
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
     * Remove the specified resource from storage.
     */
    public function createFromProject(Client $cliente)
    {
        $id = $cliente->id;
        return view('budgets.create', compact('id'));
    }
}
