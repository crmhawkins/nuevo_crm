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
        $kitDigitals = KitDigital::where('enviado', 1)->get();

        return view('kitDigital.indexWhatsapp', compact('kitDigitals'));


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

            if ($data['key'] === 'comercial_id' ) {
                if(Auth::user()->access_level_id != 1 && Auth::user()->access_level_id != 2 && Auth::user()->access_level_id != 3){
                    return response()->json([
                        'icon' => 'error',
                        'mensaje' => 'No tienes permisos para modificar este campo.'
                    ]);
                }
            }

            if ($data['key'] === 'importe') {
                // Limpia cualquier carácter no numérico excepto comas y puntos
                $value = preg_replace('/[^\d,\.]/', '', $data['value']);

                // Verifica si el tercer carácter desde la derecha es un punto o coma para determinar el separador decimal
                $thirdCharFromRight = substr($value, -3, 1);
                if ($thirdCharFromRight === ',' || $thirdCharFromRight === '.') {
                    $decimalPosition = strlen($value) - 3;
                } else {
                    // Identificar el último punto o coma como separador decimal
                    $decimalPosition = strrpos($value, ',') ?: strrpos($value, '.');
                }

                if ($decimalPosition !== false && strlen($value) - $decimalPosition > 1) {
                    // Separa la parte entera de la parte decimal
                    $integerPart = substr($value, 0, $decimalPosition);
                    $decimalPart = substr($value, $decimalPosition + 1);

                    // Elimina separadores de miles en la parte entera
                    $integerPart = str_replace([',', '.'], '', $integerPart);

                    // Reconstruye el valor usando un punto como separador decimal
                    $value = $integerPart . '.' . $decimalPart;
                } else {
                    // Si no hay separador decimal, elimina comas o puntos como separadores de miles
                    $value = str_replace([',', '.'], '', $value);
                }

                // Convierte el valor a número flotante con dos decimales
                $data['value'] = number_format((float) $value, 2, ',', '');
            }
            $valor1 = $item[$data['key']];

            $item[$data['key']] = $data['value'];

            $item->save();

            switch ($data['key']) {
                case 'gestor':
                    $valor2 = User::find($data['value'])->name;
                    $valor1 = User::find($valor1)->name;
                    break;
                case 'comercial_id':
                    $valor2 = User::find($data['value'])->name;
                    $valor1 = User::find($valor1)->name;
                    break;
                case 'servicio_id':
                    $valor2 = KitDigitalServicios::find($data['value'])->name;
                    $valor1 = KitDigitalServicios::find($valor1)->name;
                    break;
                case 'estado':
                    $valor2 = KitDigitalEstados::find($data['value'])->nombre;
                    $valor1 = KitDigitalEstados::find($valor1)->nombre;
                    break;
                case 'cliente_id':
                    $valor2 = Client::find($data['value'])->name;
                    $valor1 = Client::find($valor1)->name;
                    break;
                default:
                    $valor2 = $data['value'];
                    $valor1 = $valor1;
                    break;
            }

            LogActions::create([
                'tipo' => 1,
                'admin_user_id' => Auth::user()->id,
                'action' => 'Actualizar '. $data['key'].' en kit digital',
                'description' => 'De  "'.$valor1.'"  a  "'. $valor2.'"',
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
            'tipo' => 1,
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
            'tipo' => 1,
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

     // Vista de los mensajes
     public function whatsapp($id)
     {
          $cliente = KitDigital::find($id)->cliente;

           $curl = curl_init();

           curl_setopt_array($curl, [
               CURLOPT_URL => 'https://asistente.crmhawkins.com/listar-mensajes/'.$id,
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'POST',
               CURLOPT_HTTPHEADER => [
                   'Content-Type: application/json'
               ],
           ]);

           $response = curl_exec($curl);


           curl_close($curl);

         $mensajes = json_decode($response);
         $resultado = [];
         foreach ($mensajes as $elemento) {

             $remitenteSinPrefijo = $elemento->remitente;


             $elemento->nombre_remitente = 'Desconocido';
           $resultado[]  = $elemento;

         }

         return view('whatsapp.whatsappIndividual', compact('resultado','cliente'));
     }
}
