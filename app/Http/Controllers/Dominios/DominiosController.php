<?php

namespace App\Http\Controllers\Dominios;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Dominios\Dominio;
use App\Models\Dominios\estadosDominios;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DominiosController extends Controller
{
    public function index()
    {
        $dominios = Dominio::paginate(2);
        return view('dominios.index', compact('dominios'));
    }

    public function edit($id)
    {
        $dominio = Dominio::find($id);
        $clientes = Client::all();
        $estados = estadosDominios::all();

        return view('dominios.edit', compact('dominio','clientes','estados'));
    }
    public function create()
    {
        $clientes = Client::all();
        return view('dominios.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'dominio' => 'required|max:200',
            'client_id' => 'required',
            'date' => 'required',

        ], [
            'dominio.required' => 'El nombre es requerido para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'date.required' => 'La fecha de contratación es requerido para continuar',

        ]);

        $data = $request->all();

        // Crear una instancia de Carbon para la fecha de inicio
        $dateStart = Carbon::parse($request->input('date'));

        // Calcular la fecha de finalización agregando un año
        // $data['data_start'] = $dateStart->format('Y-m-d');
        // Calcular la fecha de finalización agregando un año
        $data['date_end'] = $dateStart->addYear()->format('Y-m-d H:i:s'); // Formato DATETIME

        // Formatear date_start para asegurarse que sea en el formato correcto
        $data['date_start'] = Carbon::parse($request->input('date_start'))->format('Y-m-d H:i:s');
        $data['estado_id'] = 2;

        $crearDominio = Dominio::create($data);


        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El dominio se creo correctamente'
        ]);

        return redirect()->route('dominios.index');
    }

    public function update(Request $request, $id)
    {

        $dominio = Dominio::find($id);
        // Validamos los campos
        $this->validate($request, [
            'dominio' => 'required|max:200',
            'client_id' => 'required',
            'date' => 'required',

        ], [
            'dominio.required' => 'El nombre es requerido para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'date.required' => 'La fecha de contratación es requerido para continuar',

        ]);

        $data = $request->all();

        // Crear una instancia de Carbon para la fecha de inicio
        $dateStart = Carbon::parse($request->input('date'));
        // dd($dateStart);

        // Calcular la fecha de finalización agregando un año
        // $data['data_start'] = $dateStart->format('Y-m-d');
        // Calcular la fecha de finalización agregando un año
        $data['date_end'] = $dateStart->addYear()->format('Y-m-d H:i:s'); // Formato DATETIME

        // Formatear date_start para asegurarse que sea en el formato correcto
        $data['date_start'] = Carbon::parse($request->input('date_start'))->format('Y-m-d H:i:s');
        $dominio->update(attributes: $data);


        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El dominio se creo correctamente'
        ]);

        return redirect()->route('dominios.index');
    }

    public function destroy(Request $request)
    {
        $domino = Dominio::find($request->id);

        if (!$domino) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $domino->delete();
        return response()->json([
            'error' => false,
            'mensaje' => 'El usuario fue borrado correctamente'
        ]);
    }
}
