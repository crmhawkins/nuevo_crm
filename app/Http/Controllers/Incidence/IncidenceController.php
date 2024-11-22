<?php

namespace App\Http\Controllers\Incidence;

use App\Http\Controllers\Controller;
use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\Incidence\Incidences;
use App\Models\Users\User;
use App\Models\Suppliers\Supplier;
use App\Models\Incidence\IncidenceStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IncidenceController extends Controller
{
    public function index()
    {
        $incidences = Incidences::paginate(10);
        return view('incidences.index', compact('incidences'));
    }

    public function create()
    {
        $clientes = Client::all();
        $presupuestos = Budget::all();
        $suppliers = Supplier::all();
        $users = User::where('inactive', 0)->get();
        $estados = IncidenceStatus::all();

        return view('incidences.create', compact('clientes', 'presupuestos', 'suppliers', 'users', 'estados'));
    }

    public function store(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'client_id' => 'nullable|exists:clients,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'gestor_id' => 'nullable',
            'admin_user_id' => 'nullable',
            'status' => 'nullable|exists:incidences_status,id',
        ], [
            'titulo.required' => 'El título es requerido para continuar',
            'descripcion.required' => 'La descripción es requerida para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'gestor_id.required' => 'El gestor es requerido para continuar',
            'admin_user_id.required' => 'El usuario administrador es requerido para continuar',
            'status.required' => 'El estado es requerido para continuar',
        ]);

        $data = $request->all();
        $incidence = Incidences::create($data);

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'La incidencia se creó correctamente'
        ]);

        return redirect()->route('incidencias.index');
    }

    public function edit($id)
    {
        $incidencia = Incidences::findOrFail($id);
        $clientes = Client::all();
        $presupuestos = Budget::all();
        $suppliers = Supplier::all();
        $users = User::where('inactive', 0)->get();
        $estados = IncidenceStatus::all();

        return view('incidences.edit', compact('incidencia', 'clientes', 'presupuestos', 'suppliers', 'users', 'estados'));
    }

    public function update(Request $request, $id)
    {
        $incidencia = Incidences::findOrFail($id);

        // Validamos los campos
        $this->validate($request, [
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'client_id' => 'nullable|exists:clients,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'gestor_id' => 'nullable',
            'admin_user_id' => 'nullable',
            'status' => 'nullable|exists:incidence_statuses,id',
        ], [
            'titulo.required' => 'El título es requerido para continuar',
            'descripcion.required' => 'La descripción es requerida para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'gestor_id.required' => 'El gestor es requerido para continuar',
            'admin_user_id.required' => 'El usuario administrador es requerido para continuar',
            'status.required' => 'El estado es requerido para continuar',
        ]);

        $data = $request->all();
        $incidencia->update($data);

        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'La incidencia se actualizó correctamente'
        ]);

        return redirect()->route('incidencias.index');
    }

    public function destroy(Request $request)
    {
        $incidencia = Incidences::find($request->id);

        if (!$incidencia) {
            return response()->json([
                'status' => true,
                'mensaje' => "Error en el servidor, intentelo más tarde."
            ]);
        }

        $incidencia->delete();
        return response()->json([
            'status' => false,
            'mensaje' => 'La incidencia fue borrada correctamente'
        ]);
    }
}
