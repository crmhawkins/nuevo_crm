<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Accounting\GrupoContable;
use App\Models\Accounting\SubGrupoContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubGrupoContabilidadController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'asc');
        $subGrupo = $request->get('subGrupo');
        $perPage = $request->get('perPage', 10);

        $query = SubGrupoContable::with('grupo');

        if ($search) {
            $query->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('numero', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%')
                  ->orWhereHas('grupo', function ($q) use ($search) {
                      $q->where('nombre', 'like', '%' . $search . '%');
                  });
        }

        if ($subGrupo) {
            $query->where('grupo_id', $subGrupo);
        }

        $response = $query->orderBy($sort, $order)->paginate($perPage);
        $subgrupos = GrupoContable::all();

        return view('contabilidad.subGrupoContabilidad.index', compact('response','subgrupos'));
    }



    public function create()
    {
        $response = 'Hola mundo';

        return view('contabilidad.grupoContabilidad.create', compact('response'));
    }


    public function edit(GrupoContable $grupoContable, Request $request)
    {
        $response = GrupoContable::find($request->id);

        return view('contabilidad.grupoContabilidad.edit', compact('response'));

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

        return redirect()->route('ingreso.index')->with('status', 'El Grupo fue creado con éxito!');

    }



    public function updated(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'required',
            'nombre' => 'required',
            'descripcion' => 'required'

        ]);
        // $this->validate(request(), [
        //     'numero' => 'required',
        //     'nombre' => 'required',

        // ]);

        if ($validator->passes()) {
            $grupo = GrupoContable::where('id', $request->id)->first();

            $grupo->numero = $request->numero;
            $grupo->nombre = $request->nombre;
            $grupo->descripcion = $request->descripcion;

            $grupo->save();

            return response()->json([
                'message' => 'Peticion guardada.',
                'entryUrl' => route('grupoContabilidad.index'),
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
            ]);        }
        $grupo->delete();

        return response()->json([
            'message' => 'Grupo Borrado.',
            'entryUrl' => route('grupoContabilidad.index'),
         ]);
    }
}
