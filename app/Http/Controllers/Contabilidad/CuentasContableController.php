<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CuentasContable;
use App\Models\Accounting\GrupoContable;
use App\Models\Accounting\SubGrupoContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

//use Yajra\DataTables\Facades\DataTables;
//use Yajra\DataTables\DataTables;

class CuentasContableController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'asc');
        $subGrupo = $request->get('subGrupo');
        $perPage = $request->get('perPage', 10);

        $query = CuentasContable::with('grupo');

        if ($search) {
            $query->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('numero', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%')
                  ->orWhereHas('grupo', function ($q) use ($search) {
                      $q->where('nombre', 'like', '%' . $search . '%');
                  });
        }

        if ($subGrupo) {
            $query->where('sub_grupo_id', $subGrupo);
        }

        $response = $query->orderBy($sort, $order)->paginate($perPage);

        $subgrupos = SubGrupoContable::all();
        return view('contabilidad.cuentaContabilidad.index', compact('response', 'subgrupos'));
    }
    public function getCuentasByDataTables(){

        $CuentasContables = CuentasContable::select('sub_grupo_id', 'numero', 'nombre', 'descripcion','id');

        return DataTables::of($CuentasContables)
                ->addColumn('subGrupo', function ($CuentasContable) {
                    if($CuentasContable->sub_grupo_id){
                        $subgrupo = SubGrupoContable::where('id', $CuentasContable->sub_grupo_id )->first();
                        return strval($subgrupo->numero .' - '.$subgrupo->nombre);
                    }
                    else{
                        return "no ";
                    }
                })



                ->addColumn('action', function ($CuentasContable) {
                    return '<a href="/admin/cuentas-contables/'.$CuentasContable->id.'/edit" class="btn btn-xs btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>';
                })
                // ->addColumn('delete', function ($CuentasContable) {
                //     $url = route('cuentasContables.destroy', [ 'id'=> $CuentasContable->id]);
                //     return '<form action="'.$url.'" method="POST" enctype="multipart/form-data" data-callback="formCallback">
                //         <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i></button>
                //     </form>';
                // })

                ->addColumn('delete', function ($CuentasContable) {
                    return '<button type="button" class="btn btn-danger" onclick="deleteEntry('.$CuentasContable->id.')" ><i class="fas fa-times"></i></button>';
                })


                ->escapeColumns(null)
                ->make();
    }

    public function create()
    {

        $grupos = GrupoContable::all();
        $subgrupos = SubGrupoContable::all();

        return view('contabilidad.cuentaContabilidad.create', compact('subgrupos', 'grupos'));
    }


    public function edit($id)
    {
        $cuenta = CuentasContable::find($id);

        $grupo = SubGrupoContable::all();

        return view('contabilidad.cuentaContabilidad.edit', compact('cuenta', 'grupo'));

    }


     /**
     * Guardar un nuevo contacto
     *
    * @param  Request  $request
    *
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
         // Validamos los datos recibidos desde el formulario
         $validator = Validator::make($request->all(), [
            'sub_grupo_id' => 'required',
            'numero' => 'required|unique:sub_grupo_contable',
            'nombre' => 'required',
        ]);

        // Comprobamos que si hemos pasado la validacion en ese caso creamos el grupo y devolvemos alerta
        if ($validator->passes()) {
            CuentasContable::create($request->all());
            return response()->json([
                'message' => 'Cuenta Creada.',
                'entryUrl' => route('cuentasContables.index'),
            ]);
        }
        // Si la validacion no a sido pasada se muestra esta alerta.

        return response()->json([
            'message' => $validator->errors()->all(),
        ]);

    }



    public function updated(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sub_grupo_id' => 'required',
            'numero' => 'required',
            'nombre' => 'required',
        ]);
        // $this->validate(request(), [
        //     'numero' => 'required',
        //     'nombre' => 'required',

        // ]);

        if ($validator->passes()) {
            $grupo = CuentasContable::where('id', $request->id)->first();
            $grupo->sub_grupo_id = $request->grupo_id;
            $grupo->numero = $request->numero;
            $grupo->nombre = $request->nombre;
            $grupo->save();

            return response()->json([
                'message' => 'Cuenta actualizada.',
                'entryUrl' => route('cuentasContables.edit', $grupo->id),
            ]);

        }
        return response()->json([
            'message' => $validator->errors()->all(),
        ]);

    }


    public function destroy($id)
    {
        $cuenta = CuentasContable::find($id);

        if (!$cuenta) {
            return response()->json([
                'message' => 'El id: ' . $id . ' no existe.',
            ]);
        }

        $cuenta->delete();

        return response()->json([
            'message' => 'Cuenta Borrada.',
            'entryUrl' => route('cuentasContables.index'),
        ]);
    }
}
