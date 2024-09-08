<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use Illuminate\Http\Request;
use App\Models\KitDigital;

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
            $item[$data['key']] = $data['value'];
            $item->save();
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
}
