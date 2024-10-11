<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KitDigital;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getayudas(Request $request){

        $kitDigitals = KitDigital::where('estado', 18 )->where(function($query) {
            $query->where('enviado', '!=', 1)
                  ->orWhereNull('enviado');
        })->get();
        // $kitDigitals = KitDigital::where(function($query) {
        //     $query->where('enviado', '!=', 1)
        //           ->orWhereNull('enviado');
        // })->get();

        return $kitDigitals;

    }
    public function updateAyudas($id){
        $kitDigital = KitDigital::find($id);
        $kitDigital->enviado = 1;
        $kitDigital->save();

        return response()->json(['success' => $id]);
    }

    public function updateMensajes(Request $request)
    {
       // Storage::disk('local')->put('Respuesta_Peticion_ChatGPT-Model.txt', $request->all() );
            $ayuda = KitDigital::find($request->ayuda_id);

            $ayuda->mensaje = $request->mensaje;
            $ayuda->mensaje_interpretado = $request->mensaje_interpretado;
            $actualizado = $ayuda->save();

        if($actualizado){
            return response()->json([
                'success' => true,
                'ayudas' => 'Actualizado con exito',
                'result'=> $ayuda
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'ayudas' => 'Error al Actualizar.'
            ], 200);
        }

    }

}
