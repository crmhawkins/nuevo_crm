<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnvioDani;
use App\Models\KitDigital;
use App\Models\Whatsapp\Mensaje;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function getayudas(Request $request){
        $segmentos = ['A', 'B', 'C'];  // Define los segmentos que deseas incluir

        $kitDigitals = KitDigital::where('estado', 18)
                         ->whereIn('segmento', $segmentos)  // Usar whereIn para múltiples valores
                         ->where(function($query) {
                             $query->where('enviado', '!=', 1)
                                   ->orWhereNull('enviado');
                         })
                         ->whereNotNull('telefono')  // Asegurarse de que 'telefono' no sea nulo
                         ->limit(10)  // Limitar la consulta a 10 registros
                         ->get();

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
        if($request->ayuda_id != null){
            $ayuda = KitDigital::find($request->ayuda_id);
            $ayuda->enviado = 1;
            if($request->mensaje != null){
                $ayuda->mensaje = $request->mensaje;
                $ayuda->mensaje_interpretado = $request->mensaje_interpretado;
            }
            $ayuda->save();
        }

        $isAutomatico = Mensaje::where('remitente', $request->remitente)
            ->where('is_automatic', true)
            ->where('mensaje', null)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'desc')
            ->first();

        if($isAutomatico && $request->is_automatic == false) {
            $mensaje = $request->mensaje;
            $isAutomatico ->mensaje = $mensaje;
            $actualizado = $isAutomatico ->save();

        }else {
            $dataRegistrar = [
                'id_mensaje' => $request->id_mensaje,
                'id_three' => null,
                'remitente' => $request->remitente,
                'mensaje' => $request->mensaje,
                'respuesta' => $request->respuesta,
                'status' => $request->status,
                'status_mensaje' => $request->status_mensaje,
                'is_automatic' => $request->is_automatic,
                'ayuda_id'=>$request->ayuda_id,
                'type' => 'text',
                'date' => Carbon::now()
            ];
        $mensajeCreado = Mensaje::create($dataRegistrar);
        $actualizado = isset($mensajeCreado);
        }

        if($actualizado){
            Storage::disk('local')->put("Request".Carbon::now()."_Update_Mensajes.txt", implode($request->all()));

            return response()->json([
                'success' => true,
                'ayudas' => 'Actualizado con exito',
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'ayudas' => 'Error al Actualizar.'
            ], 200);
        }

    }

}
