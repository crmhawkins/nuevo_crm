<?php

namespace App\Http\Controllers;

use App\Models\Accounting\GrupoContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GrupoContabilidadController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->get('search');
        // Establece 'numero' como el campo de ordenamiento por defecto.
        $sort = $request->get('sort', 'numero');
        // Establece 'asc' como el orden por defecto.
        $order = $request->get('order', 'asc');
        $perPage = $request->get('perPage', 10);

        $query = GrupoContable::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                ->orWhere('numero', 'like', '%' . $search . '%')
                ->orWhere('descripcion', 'like', '%' . $search . '%');
            });
        }

        // Ejecuta la ordenación y paginación en la consulta.
        $response = $query->orderBy($sort, $order)->paginate($perPage);

        return view('admin.contabilidad.grupoContabilidad.index', compact('response'));
    }



    public function create()
    {
        $response = 'Hola mundo';

        return view('admin.contabilidad.grupoContabilidad.create', compact('response'));
    }


    public function edit(GrupoContable $grupoContable, Request $request)
    {
        $response = GrupoContable::find($request->id);

        return view('admin.contabilidad.grupoContabilidad.edit', compact('response'));

    }



    public function store(Request $request)
    {

        // Validamos los datos recibidos desde el formulario
        $rules = [
            'numero' => 'required',
            'nombre' => 'required',
            'descripcion' => 'required'

        ];

        $validatedData = $request->validate($rules);
        GrupoContable::create($request->all());

        return redirect()->route('admin.ingresos.index')->with('status', 'El Grupo fue creado con éxito!');

    }



    public function updated(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'required',
            'nombre' => 'required',
            'descripcion' => 'required'

        ]);


        if ($validator->passes()) {
            $grupo = GrupoContable::where('id', $request->id)->first();

            $grupo->numero = $request->numero;
            $grupo->nombre = $request->nombre;
            $grupo->descripcion = $request->descripcion;

            $grupo->save();

            return response()->json([
                'message' => 'Peticion guardada.',
                'entryUrl' => route('admin.grupoContabilidad.index'),
             ]);

        }
        return response()->json([
            'message' => $validator->errors()->all(),
         ]);

    }

    /**
     * Borrar contacto
     *
     * @param  GrupoContable  $contact
     * @param  Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $grupo = GrupoContable::find($id);

        if ($grupo == null) {
            return response()->json([
                'message' => 'El id: ' . $id . ' no existe.',
            ]);
        }
        $grupo->delete();

        return response()->json([
            'message' => 'Grupo Borrado.',
            'entryUrl' => route('admin.grupoContabilidad.index'),
         ]);;
    }
}
