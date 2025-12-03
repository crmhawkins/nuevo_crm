<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\EnvioB2b;
use App\Models\EnvioDani;
use App\Models\KitDigital;
use App\Models\Whatsapp\Mensaje;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function getayudas(Request $request){

        $kitDigitals = EnvioB2b::where('enviado', 0)->limit(50)  // Limitar la consulta a 10 registros
                         ->get();

        return $kitDigitals;
    }

    public function getClientes(Request $request){
        $search = $request->input('search');

        if ($search) {
            $clientes = Client::where('is_client', 1)
                ->where(function($query) use ($search) {
                    $query->where('id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cif', 'like', "%{$search}%");
                })
                ->get();
            return response()->json($clientes);
        } else {
            return response()->json(['error' => 'No se proporcionó un criterio de búsqueda']);
        }
    }

    public function getClientesContactos(Request $request){
        // Primero obtenemos los IDs de los contactos asociados al cliente desde la tabla pivote clients_x_contacts
        $idCliente = $request->input('id_cliente');
        if ($idCliente) {
            $cliente = Client::find($idCliente);

            // Ahora obtenemos los contactos correspondientes a esos IDs
            $contactos = $cliente->contacto;
            return response()->json($contactos);
        } else {
            return response()->json(['error' => 'ID de cliente no proporcionado']);
        }

    }

    public function updateAyudas($id){
        $kitDigital = EnvioB2b::find($id);
        $kitDigital->enviado = 1;
        $kitDigital->save();

        return response()->json(['success' => $id]);
    }

    public function updateMensajes(Request $request)
    {
        if($request->ayuda_id != null){
            $ayuda = EnvioB2b::find($request->ayuda_id);
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

    public function getClients()
    {
        $clients = Client::all();

        return response()->json($clients, 200);
    }

}
