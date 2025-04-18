<?php

namespace App\Http\Controllers;

use App\Exports\AyudasExport;
use App\Models\Clients\Client;
use App\Models\EnvioB2b;
use Illuminate\Http\Request;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\KitDigitalServicios;
use App\Models\Logs\LogActions;
use App\Models\Users\User;
use App\Models\Whatsapp\Mensaje;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Optional;
use Maatwebsite\Excel\Facades\Excel;

class KitDigitalController extends Controller
{
    public function index(){
        return view('kitDigital.indexWhatsapp');
    }

    public function listarClientes(Request $request){

        // Variables de filtro
        $selectedCliente = $request->input('selectedCliente');
        $selectedEstado = $request->input('selectedEstado');
        $selectedGestor = $request->input('selectedGestor');
        $selectedServicio = $request->input('selectedServicio');
        $selectedEstadoFactura = $request->input('selectedEstadoFactura');
        $selectedComerciales = $request->input('selectedComerciales');
        $selectedSegmento = $request->input('selectedSegmento');
        $selectedDateField = $request->input('selectedDateField');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sortColumn = $request->input('sortColumn', 'created_at'); // Columna por defecto
        $sortDirection = $request->input('sortDirection', 'desc'); // Dirección por defecto
        $perPage = $request->input('perPage', 10);

        // Cargando datos estáticos
        $gestores = cache()->remember('gestores', 60, fn() => User::where('access_level_id', 4)->where('inactive', 0)->get());
        $comerciales = cache()->remember('comerciales', 60, fn() => User::whereIn('access_level_id', [1, 6])->get());
        $servicios = cache()->remember('servicios', 60, fn() => KitDigitalServicios::all());
        $estados = cache()->remember('estados', 60, fn() => KitDigitalEstados::orderBy('orden', 'asc')->get());
        $clientes = cache()->remember('clientes', 60, fn() => Client::where('is_client', true)->get());


        $estados_facturas = [
            ['id' => '0', 'nombre' => 'No abonada'],
            ['id' => '1', 'nombre' => 'Abonada'],
        ];
        $segmentos  = [
            ['id' => '1', 'nombre' => '1'],
            ['id' => '2', 'nombre' => '2'],
            ['id' => '3', 'nombre' => '3'],
            ['id' => '30', 'nombre' => '3 Extra'],
            ['id' => '4', 'nombre' => '4'],
            ['id' => '5', 'nombre' => '5'],
            ['id' => 'A', 'nombre' => 'A'],
            ['id' => 'B', 'nombre' => 'B'],
            ['id' => 'C', 'nombre' => 'C']
        ];

        // Construcción de la consulta principal
        $query = KitDigital::query();
        $query->with(['estados', 'Client', 'comercial','servicios']);
        // Aplicar filtros
        if ($selectedCliente) {
            $query->where('cliente_id', $selectedCliente);
        }

        if ($selectedEstado) {
            $query->where('estado', $selectedEstado);
        }

        if ($selectedGestor) {
            $query->where('gestor', $selectedGestor);
        }

        if ($selectedServicio) {
            $query->where('servicio_id', $selectedServicio);
        }

        if ($selectedEstadoFactura) {
            $query->where('estado_factura', $selectedEstadoFactura);
        }

        if ($selectedComerciales) {
            $query->where('comercial_id', $selectedComerciales);
        }

        if ($selectedSegmento) {
            $query->where('segmento', $selectedSegmento);
        }

        if ($dateFrom && $dateTo && $selectedDateField) {
            $query->whereBetween($selectedDateField, [$dateFrom, $dateTo]);
        }

        if ($buscar = $request->input('buscar')) {
            $buscarLower = mb_strtolower(trim($buscar), 'UTF-8');  // Convertir la cadena a minúsculas y eliminar espacios al inicio y al final
            $searchTerms = explode(" ", $buscarLower);  // Dividir la entrada en términos individuales

            $query->where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->Where(function ($subQuery) use ($term) {
                        $subQuery->orWhereRaw('LOWER(contratos) LIKE ?', ["%{$term}%"])
                                 ->orWhereRaw('LOWER(cliente) LIKE ?', ["%{$term}%"])
                                 ->orWhereRaw('LOWER(expediente) LIKE ?', ["%{$term}%"])
                                 ->orWhereRaw('LOWER(contacto) LIKE ?', ["%{$term}%"])
                                 ->orWhereRaw('LOWER(importe) LIKE ?', ["%{$term}%"])
                                 ->orWhereRaw('LOWER(telefono) LIKE ?', ["%{$term}%"]);
                    });
                }
            });
        }

        $Sumatorio = $query->get(['importe'])->sum(function ($item) {
            $cleanImporte = preg_replace('/[^\d,]/', '', $item->importe); // Elimina caracteres no numéricos
            $cleanImporte = str_replace(',', '.', $cleanImporte); // Cambia comas por puntos
            return (float)$cleanImporte;
        });

        $query->orderBy($sortColumn, $sortDirection);
        // Aplicar ordenación y paginación
        $kitDigitals = $perPage === 'all' ? $query->get() : $query->paginate(is_numeric($perPage) ? $perPage : 10);



        return view('kitDigital.listarClientes', compact(
            'kitDigitals',
            'gestores',
            'comerciales',
            'servicios',
            'estados',
            'clientes',
            'estados_facturas',
            'Sumatorio',
            'buscar',
            'selectedCliente',
            'selectedEstado',
            'selectedGestor',
            'selectedServicio',
            'selectedEstadoFactura',
            'selectedComerciales',
            'selectedSegmento',
            'dateFrom',
            'dateTo',
            'sortColumn',
            'sortDirection',
            'perPage',
            'segmentos',
            'selectedDateField'

        ));

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
                    $valor2 = Optional(User::find($data['value']))->name ?? 'Gestor no seleccionado';
                    $valor1 = Optional(User::find($valor1))->name ?? 'Gestor no encontrado';
                    break;
                case 'comercial_id':
                    $valor2 = Optional(User::find($data['value']))->name ?? 'Comercial no seleccionado';
                    $valor1 = Optional(User::find($valor1))->name ?? 'Comercial no encontrado';
                    break;
                case 'servicio_id':
                    $valor2 = Optional(KitDigitalServicios::find($data['value']))->name ?? 'Servicio no seleccionado';
                    $valor1 = Optional(KitDigitalServicios::find($valor1))->name ?? 'Servicio no encontrado';
                    break;
                case 'estado':
                    $valor2 = Optional(KitDigitalEstados::find($data['value']))->nombre ?? 'Estado no seleccionado';
                    $valor1 = Optional(KitDigitalEstados::find($valor1))->nombre ?? 'Estado no encontrado';
                    break;
                case 'cliente_id':
                    $valor2 = Optional(Client::find($data['value']))->name ?? 'Cliente no seleccionado';
                    $valor1 = Optional(Client::find($valor1))->name ?? 'Cliente no encontrado';
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
    public function createComercial(){
        $usuario = Auth::user();
        $servicios = KitDigitalServicios::all();
        $estados = KitDigitalEstados::orderBy('nombre', 'asc')->get();
        $clientes = Client::where('is_client', true)->get();
        $gestores = User::where('access_level_id', 4)->where('inactive', 0)->get();
        $comerciales = User::where('access_level_id', 6)->where('inactive', 0)->orWhere('access_level_id', 11)->get();

        return view('kitDigital.createComercial', compact('usuario','clientes','servicios', 'estados', 'gestores','comerciales'));
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

        $secretKey = env('NOCAPTCHA_SECRET');
        $captcha = $request->input('g-recaptcha-response');
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
        $responseKeys = json_decode($response, true);

        if(intval($responseKeys["success"]) !== 1) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Error en la verificación de reCAPTCHA. Inténtalo de nuevo.'
             ]);
        }
        $data =  $this->validate($request,[
            'cliente' => 'required',
            'nif' => 'required',
            'email' => 'required',
            'telefono' => 'required',
            'direccion' => 'required',
            'cp' => 'required',
            'ciudad' => 'required',
            'comercial_id' => 'nullable',
            'comentario' => 'nullable',
        ],[
            'cliente.required' => 'El campo es obligatorio.',
            'nif' => 'El campo es obligatorio',
            'email' => 'El campo es obligatorio',
            'telefono' => 'El campo es obligatorio',
            'direccion' => 'El campo es obligatorio',
            'cp' => 'El campo es obligatorio',
            'ciudad' => 'El campo es obligatorio',
        ]);
        $data['estado'] = 18;

        $kit = KitDigital::create($data);
        LogActions::create([
            'tipo' => 1,
            'action' => 'Crear kit digital',
            'description' => 'Crear kit digital por formulario publico',
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
        $ayuda = EnvioB2b::find($id);
        $cliente = $ayuda->nombre;
        $remitente = '34'.$ayuda->telefono;
        $primerMensaje = Mensaje::where('ayuda_id', $id)->first();

        if($primerMensaje){

            $mensajes = Mensaje::where('remitente', $primerMensaje->remitente)->get();
        }else{
            $mensajes = Mensaje::where('remitente', $remitente)->get();
        }


        $resultado = [];
        foreach ($mensajes as $elemento) {

            $remitenteSinPrefijo = $elemento->remitente;


            $elemento->nombre_remitente = 'Desconocido';
           $resultado[]  = $elemento;

        }

        return view('whatsapp.whatsappIndividual', compact('resultado','cliente'));
     }


    public function exportToExcel(Request $request)
    {
        // Variables de filtro
        $selectedCliente = $request->input('selectedCliente');
        $selectedEstado = $request->input('selectedEstado');
        $selectedGestor = $request->input('selectedGestor');
        $selectedServicio = $request->input('selectedServicio');
        $selectedEstadoFactura = $request->input('selectedEstadoFactura');
        $selectedComerciales = $request->input('selectedComerciales');
        $selectedSegmento = $request->input('selectedSegmento');
        $selectedDateField = $request->input('selectedDateField');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sortColumn = $request->input('sortColumn', 'created_at'); // Columna por defecto
        $sortDirection = $request->input('sortDirection', 'desc'); // Dirección por defecto
        // Construcción de la consulta principal
        $query = KitDigital::query();

        // Aplicar filtros
        if ($selectedCliente) {
            $query->where('cliente_id', $selectedCliente);
        }

        if ($selectedEstado) {
            $query->where('estado', $selectedEstado);
        }

        if ($selectedGestor) {
            $query->where('gestor', $selectedGestor);
        }

        if ($selectedServicio) {
            $query->where('servicio_id', $selectedServicio);
        }

        if ($selectedEstadoFactura) {
            $query->where('estado_factura', $selectedEstadoFactura);
        }

        if ($selectedComerciales) {
            $query->where('comercial_id', $selectedComerciales);
        }

        if ($selectedSegmento) {
            $query->where('segmento', $selectedSegmento);
        }

        if ($dateFrom && $dateTo && $selectedDateField) {
            $query->whereBetween($selectedDateField, [$dateFrom, $dateTo]);
        }

        if ($buscar = $request->input('buscar')) {
            $buscarLower = mb_strtolower(trim($buscar), 'UTF-8');  // Convertir la cadena a minúsculas y eliminar espacios al inicio y al final
            $searchTerms = explode(" ", $buscarLower);  // Dividir la entrada en términos individuales

            $query->where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->Where(function ($subQuery) use ($term) {
                        $subQuery->orWhereRaw('LOWER(contratos) LIKE ?', ["%{$term}%"])
                                    ->orWhereRaw('LOWER(cliente) LIKE ?', ["%{$term}%"])
                                    ->orWhereRaw('LOWER(expediente) LIKE ?', ["%{$term}%"])
                                    ->orWhereRaw('LOWER(contacto) LIKE ?', ["%{$term}%"])
                                    ->orWhereRaw('LOWER(importe) LIKE ?', ["%{$term}%"])
                                    ->orWhereRaw('LOWER(telefono) LIKE ?', ["%{$term}%"]);
                    });
                }
            });
        }


        $query->orderBy($sortColumn, $sortDirection);
        // Aplicar ordenación y paginación
        $kitDigitals =  $query->get();
        // Exporta los datos a Excel
        return Excel::download(new AyudasExport($kitDigitals), 'KitDigital.xlsx');
    }
}
