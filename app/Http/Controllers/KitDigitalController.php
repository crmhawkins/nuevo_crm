<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use Illuminate\Http\Request;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\KitDigitalServicios;
use App\Models\Logs\LogActions;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KitDigitalController extends Controller
{
    public function index(){
        $kitDigitals = KitDigital::all();



    }

    public function listarClientes(){
        $kitDigitals = KitDigital::all();

        return view('kitDigital.listarClientes', compact('kitDigitals'));
    }

    public function updateData(Request $request){
        $data = $request->all();


        if ($data['id']) {
            $item = KitDigital::find($data['id']);
            if(!$item){
                return response()->json([
                    'icon' => 'error',
                    'mensaje' => 'El registro no se encontro.'
                ]);
            }
            $valor = $item[$data['key']];
            $item[$data['key']] = $data['value'];
            $item->save();
            LogActions::create([
                'admin_user_id' => Auth::user()->id,
                'action' => 'Actualizar '. $item[$data['key']].' en kit digital',
                'description' => 'De '.$valor.' a '. $item[$data['key']],
                'reference_id' => $item->id,
            ]);

            return response()->json([
                'icon' => 'success',
                'mensaje' => 'El registro se actualizo correctamente'
            ]);
        }
        return response()->json([
            'error' => 'error',
            'mensaje' => 'El registro no se encontro.'
        ]);
    }

    public function create(){
        $usuario = Auth::user();
        $servicios = KitDigitalServicios::all();
        $estados = KitDigitalEstados::orderBy('nombre', 'asc')->get();
        $clientes = Client::where('is_client', true)->get();
        $gestores = User::where('access_level_id', 4)->where('inactive', 0)->get();
        $comerciales = User::where('access_level_id', 6)->where('inactive', 0)->orWhere('access_level_id', 11)->get();

        return view('kitDigital.create', compact('usuario','clientes','servicios', 'estados', 'gestores','comerciales'));
    }

    public function store(Request $request){

        Carbon::setLocale("es");
        $request->validate( [
            'empresa' => 'required',
            'segmento' => 'required',
            'cliente' => 'required',
            'estado' => 'required',
            'gestor' => 'required',
        ]);

        $data = $request->all();
        $kit = KitDigital::create($data);
        LogActions::create([
            'admin_user_id' => Auth::user()->id,
            'action' => 'Crear kit digital',
            'description' => 'Crear kit digital',
            'reference_id' => $kit->id,
        ]);
        return redirect()->route('kitDigital.index')->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Nuevo kit digital se guardó correctamente'
             ]);
    }
    public function storeComercial(Request $request){

        $this->validate($request,[
            'cliente' => 'required',
            'telefono' => 'required',
            'segmento' => 'required',
            'estado' => 'required',
        ],[
            'cliente.required' => 'El campo es obligatorio.',
            'telefono.required' => 'El campo es obligatorio.',
            'segmento.required' => 'El campo es obligatorio.',
            'estado.required' => 'El campo es obligatorio.',
        ]);
        $data = $request->all();
        $data['comercial_id'] = Auth::user()->id;
        $kit = KitDigital::create($data);
        LogActions::create([
            'admin_user_id' => Auth::user()->id,
            'action' => 'Crear kit digital',
            'description' => 'Crear kit digital por comercial',
            'reference_id' => $kit->id,
        ]);
        return redirect()->back()->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Nuevo kit digital se guardó correctamente'
             ]);
    }
}
