<?php

namespace App\Http\Controllers\Dominios;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Dominios\Dominio;
use App\Models\Dominios\estadosDominios;
use App\Models\Invoices\InvoiceConcepts;
use App\Models\Invoices\Invoice;
use App\Services\IonosApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DominiosController extends Controller
{
    public function index()
    {
        $dominios = Dominio::paginate(2);
        return view('dominios.index', compact('dominios'));
    }

    public function show($id)
    {
        $dominio = Dominio::with(['cliente', 'estadoName'])->find($id);

        if (!$dominio) {
            return redirect()->route('dominios.index')->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Dominio no encontrado'
            ]);
        }

        // Buscar facturas asociadas a este dominio
        $facturasAsociadas = $this->buscarFacturasAsociadas($dominio->dominio, $dominio->client_id);

        return view('dominios.show', compact('dominio', 'facturasAsociadas'));
    }

    public function edit($id)
    {
        $dominio = Dominio::find($id);
        $clientes = Client::all();
        $estados = estadosDominios::all();

        return view('dominios.edit', compact('dominio','clientes','estados'));
    }
    public function create()
    {
        $clientes = Client::all();
        return view('dominios.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        // Validamos los campos
        $this->validate($request, [
            'dominio' => 'required|max:200',
            'client_id' => 'required',
            'date' => 'required',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'iban' => 'nullable|string|max:100',

        ], [
            'dominio.required' => 'El nombre es requerido para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'date.required' => 'La fecha de contratación es requerido para continuar',
            'precio_compra.numeric' => 'El precio de compra debe ser un número válido',
            'precio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'iban.string' => 'El IBAN debe ser una cadena de texto válida',

        ]);

        $data = $request->all();

        // Crear una instancia de Carbon para la fecha de inicio
        $dateStart = Carbon::parse($request->input('date'));

        // Calcular la fecha de finalización agregando un año
        // $data['data_start'] = $dateStart->format('Y-m-d');
        // Calcular la fecha de finalización agregando un año
        $data['date_end'] = $dateStart->addYear()->format('Y-m-d H:i:s'); // Formato DATETIME

        // Formatear date_start para asegurarse que sea en el formato correcto
        $data['date_start'] = Carbon::parse($request->input('date_start'))->format('Y-m-d H:i:s');
        $data['estado_id'] = 3;

        $crearDominio = Dominio::create($data);


        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El dominio se creo correctamente'
        ]);

        return redirect()->route('dominios.index');
    }

    public function update(Request $request, $id)
    {

        $dominio = Dominio::find($id);
        // Validamos los campos
        $this->validate($request, [
            'dominio' => 'required|max:200',
            'client_id' => 'required',
            'date' => 'required',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'iban' => 'nullable|string|max:100',

        ], [
            'dominio.required' => 'El nombre es requerido para continuar',
            'client_id.required' => 'El cliente es requerido para continuar',
            'date.required' => 'La fecha de contratación es requerido para continuar',
            'precio_compra.numeric' => 'El precio de compra debe ser un número válido',
            'precio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'iban.string' => 'El IBAN debe ser una cadena de texto válida',

        ]);

        $data = $request->all();

        // Crear una instancia de Carbon para la fecha de inicio
        $dateStart = Carbon::parse($request->input('date'));
        // dd($dateStart);

        // Calcular la fecha de finalización agregando un año
        // $data['data_start'] = $dateStart->format('Y-m-d');
        // Calcular la fecha de finalización agregando un año
        $data['date_end'] = $dateStart->addYear()->format('Y-m-d H:i:s'); // Formato DATETIME

        // Formatear date_start para asegurarse que sea en el formato correcto
        $data['date_start'] = Carbon::parse($request->input('date_start'))->format('Y-m-d H:i:s');
        $dominio->update(attributes: $data);


        session()->flash('toast', [
            'icon' => 'success',
            'mensaje' => 'El dominio se creo correctamente'
        ]);

        return redirect()->route('dominios.index');
    }

    public function destroy(Request $request)
    {
        $domino = Dominio::find($request->id);

        if (!$domino) {
            return response()->json([
                'error' => true,
                'mensaje' => "Error en el servidor, intentelo mas tarde."
            ]);
        }

        $domino->delete();
        return response()->json([
            'error' => false,
            'mensaje' => 'El usuario fue borrado correctamente'
        ]);
    }

    /**
     * Buscar facturas asociadas a un dominio
     */
    private function buscarFacturasAsociadas($domainName, $clienteId)
    {
        // Normalizar el dominio para búsqueda
        $normalizedDomain = $this->normalizeDomain($domainName);

        // Buscar en conceptos de facturas del mismo cliente
        $conceptos = InvoiceConcepts::with(['invoice.budget.cliente', 'invoice.invoiceStatus'])
            ->whereHas('invoice.budget', function($query) use ($clienteId) {
                $query->where('client_id', $clienteId);
            })
            ->where(function($query) use ($normalizedDomain) {
                $query->where('title', 'like', "%{$normalizedDomain}%")
                      ->orWhere('concept', 'like', "%{$normalizedDomain}%")
                      // Buscar también por la palabra "dominio" en diferentes casos
                      ->orWhere('title', 'like', '%dominio%')
                      ->orWhere('concept', 'like', '%dominio%')
                      ->orWhere('title', 'like', '%Dominio%')
                      ->orWhere('concept', 'like', '%Dominio%')
                      ->orWhere('title', 'like', '%DOMINIO%')
                      ->orWhere('concept', 'like', '%DOMINIO%');
            })
            ->get();

        $facturas = collect();

        foreach ($conceptos as $concepto) {
            if ($concepto->invoice &&
                $concepto->invoice->budget &&
                $concepto->invoice->budget->cliente &&
                $concepto->invoice->budget->client_id == $clienteId &&
                !$facturas->contains('id', $concepto->invoice->id)) {
                $facturas->push($concepto->invoice);
            }
        }

        return $facturas;
    }

    /**
     * Normalizar dominio para búsqueda
     */
    private function normalizeDomain($domain)
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '/'); // Eliminar barra final
        return $domain;
    }

    /**
     * Cambiar estado del dominio a cancelado
     */
    public function cancelar($id)
    {
        try {
            $dominio = Dominio::find($id);

            if (!$dominio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dominio no encontrado'
                ], 404);
            }

            $dominio->update(['estado_id' => 2]); // ID 2 = Cancelado

            return response()->json([
                'success' => true,
                'message' => 'Dominio cancelado exitosamente',
                'estado' => 'Cancelado'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el dominio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar el estado HTTP del dominio
     */
    public function verificarEstado($id)
    {
        try {
            $dominio = Dominio::find($id);

            if (!$dominio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dominio no encontrado'
                ], 404);
            }

            $url = $this->normalizarUrl($dominio->dominio);
            $estado = $this->verificarEstadoHttp($url);

            return response()->json([
                'success' => true,
                'url' => $url,
                'estado' => $estado['codigo'],
                'descripcion' => $estado['descripcion'],
                'clase' => $estado['clase'],
                'tiempo_respuesta' => $estado['tiempo_respuesta'] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el dominio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normalizar URL para verificación
     */
    private function normalizarUrl($dominio)
    {
        $dominio = trim($dominio);

        // Si no tiene protocolo, agregar https://
        if (!preg_match('/^https?:\/\//', $dominio)) {
            $dominio = 'https://' . $dominio;
        }

        return $dominio;
    }

    /**
     * Verificar estado HTTP del dominio
     */
    private function verificarEstadoHttp($url)
    {
        $inicio = microtime(true);

        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'HEAD',
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; DomainChecker/1.0)',
                    'follow_location' => true,
                    'max_redirects' => 5
                ]
            ]);

            $headers = @get_headers($url, 1, $context);
            $tiempo = round((microtime(true) - $inicio) * 1000, 2);

            if ($headers === false) {
                return [
                    'codigo' => 'ERROR',
                    'descripcion' => 'No se pudo conectar al dominio',
                    'clase' => 'danger',
                    'tiempo_respuesta' => null
                ];
            }

            $codigo = $this->extraerCodigoHttp($headers[0]);
            $descripcion = $this->obtenerDescripcionEstado($codigo);
            $clase = $this->obtenerClaseEstado($codigo);

            return [
                'codigo' => $codigo,
                'descripcion' => $descripcion,
                'clase' => $clase,
                'tiempo_respuesta' => $tiempo
            ];

        } catch (\Exception $e) {
            return [
                'codigo' => 'ERROR',
                'descripcion' => 'Error al verificar: ' . $e->getMessage(),
                'clase' => 'danger',
                'tiempo_respuesta' => null
            ];
        }
    }

    /**
     * Extraer código HTTP de los headers
     */
    private function extraerCodigoHttp($header)
    {
        if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
            return (int)$matches[1];
        }
        return 'ERROR';
    }

    /**
     * Obtener descripción amigable del estado HTTP
     */
    private function obtenerDescripcionEstado($codigo)
    {
        $estados = [
            200 => 'Sitio web funcionando correctamente',
            201 => 'Recurso creado exitosamente',
            202 => 'Solicitud aceptada',
            204 => 'Sin contenido',
            301 => 'Redirección permanente',
            302 => 'Redirección temporal',
            304 => 'Contenido no modificado',
            400 => 'Solicitud incorrecta',
            401 => 'Acceso no autorizado',
            403 => 'Acceso prohibido',
            404 => 'Página no encontrada',
            405 => 'Método no permitido',
            408 => 'Tiempo de espera agotado',
            410 => 'Contenido no disponible',
            429 => 'Demasiadas solicitudes',
            500 => 'Error interno del servidor',
            502 => 'Puerta de enlace incorrecta',
            503 => 'Servicio no disponible',
            504 => 'Tiempo de espera de puerta de enlace',
            505 => 'Versión HTTP no soportada'
        ];

        return $estados[$codigo] ?? 'Estado desconocido';
    }

    /**
     * Obtener clase CSS para el estado
     */
    private function obtenerClaseEstado($codigo)
    {
        if ($codigo === 'ERROR') {
            return 'danger';
        }

        if ($codigo >= 200 && $codigo < 300) {
            return 'success';
        } elseif ($codigo >= 300 && $codigo < 400) {
            return 'warning';
        } elseif ($codigo >= 400 && $codigo < 500) {
            return 'danger';
        } elseif ($codigo >= 500) {
            return 'danger';
        }

        return 'secondary';
    }

    /**
     * Sincronizar información de IONOS para un dominio específico
     */
    public function sincronizarIonos($id)
    {
        try {
            $dominio = Dominio::find($id);

            if (!$dominio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dominio no encontrado'
                ], 404);
            }

            $ionosService = new IonosApiService();
            $result = $ionosService->getDomainInfo($dominio->dominio);

            if ($result['success']) {
                $dominio->update([
                    'fecha_activacion_ionos' => $result['fecha_activacion_ionos'],
                    'fecha_renovacion_ionos' => $result['fecha_renovacion_ionos'],
                    'ionos_id' => $result['ionos_id'],
                    'sincronizado_ionos' => true,
                    'ultima_sincronizacion_ionos' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dominio sincronizado con IONOS exitosamente',
                    'data' => [
                        'fecha_activacion' => $dominio->fecha_activacion_ionos_formateada,
                        'fecha_renovacion' => $dominio->fecha_renovacion_ionos_formateada
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al sincronizar con IONOS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información de IONOS para un dominio
     */
    public function obtenerInfoIonos($id)
    {
        try {
            $dominio = Dominio::find($id);

            if (!$dominio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dominio no encontrado'
                ], 404);
            }

            $ionosService = new IonosApiService();
            $result = $ionosService->getDomainInfo($dominio->dominio);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de IONOS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Probar conexión con la API de IONOS
     */
    public function probarConexionIonos()
    {
        try {
            $ionosService = new IonosApiService();
            $result = $ionosService->testConnection();

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al probar conexión: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ejecutarComandoIonos(Request $request)
    {
        try {
            $comando = $request->input('comando');

            if (!$comando) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comando no especificado'
                ], 400);
            }

            // Validar que el comando sea uno de los permitidos
            $comandosPermitidos = [
                'ionos:sync-missing-dates',
                'ionos:sync-all-missing',
                'ionos:analyze-missing',
                'ionos:sync-all',
                'ionos:update-all-dates',
                'test:web-command'
            ];

            if (!in_array($comando, $comandosPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comando no permitido'
                ], 400);
            }

            // Ejecutar el comando
            $output = [];
            $returnCode = 0;

            // Usar la ruta completa del directorio del proyecto
            $projectPath = base_path();
            $command = "cd {$projectPath} && php artisan {$comando} --limit=10";
            exec($command . ' 2>&1', $output, $returnCode);

            $outputString = implode("\n", $output);

            if ($returnCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Comando ejecutado exitosamente:\n" . $outputString
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Error al ejecutar comando:\n" . $outputString
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al ejecutar comando: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular fecha de registro basada en fecha de renovación IONOS
     */
    public function calcularFechaRegistro($id)
    {
        try {
            $dominio = Dominio::find($id);

            if (!$dominio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dominio no encontrado'
                ], 404);
            }

            if (!$dominio->fecha_renovacion_ionos) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay fecha de renovación IONOS disponible para calcular la fecha de registro'
                ], 400);
            }

            $resultado = $dominio->calcularFechaRegistro();

            if ($resultado) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fecha de registro calculada exitosamente',
                    'data' => [
                        'fecha_registro_calculada' => $dominio->fecha_registro_calculada_formateada,
                        'fecha_renovacion_ionos' => $dominio->fecha_renovacion_ionos_formateada,
                        'diferencia_anos' => 1
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al calcular la fecha de registro'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular fecha de registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener teléfonos de clientes de dominios filtrados para batch calls
     */
    public function obtenerTelefonosClientesDominios(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('=== INICIO obtenerTelefonosClientesDominios ===');
            \Illuminate\Support\Facades\Log::info('Datos recibidos:', $request->all());

            $añoActual = date('Y');

            // Aplicar los mismos filtros que usa el componente Livewire
            $query = Dominio::with(['cliente'])
                    ->where('estado_id', '!=', 2) // Excluir dominios cancelados
                    ->when($request->buscar, function ($query) use ($request) {
                        $query->where('dominio', 'like', '%' . $request->buscar . '%');
                    })
                    ->when($request->selectedCliente, function ($query) use ($request) {
                        $query->where('client_id', $request->selectedCliente);
                    })
                    ->when($request->selectedEstado, function ($query) use ($request) {
                        $query->where('estado_id', $request->selectedEstado);
                    })
                    ->when($request->fechaInicio, function ($query) use ($request) {
                        $query->where('fecha_renovacion_ionos', '>=', $request->fechaInicio);
                    })
                    ->when($request->fechaFin, function ($query) use ($request) {
                        $query->where('fecha_renovacion_ionos', '<=', $request->fechaFin);
                    })
                    ->when($request->filtroSinFacturas == '1' && $request->añoSinFacturas, function ($query) use ($request) {
                        $query->whereNull('fecha_registro_calculada')
                              ->whereNotExists(function ($subQuery) use ($request) {
                                  $subQuery->select(DB::raw(1))
                                          ->from('budgets')
                                          ->join('invoices', 'budgets.id', '=', 'invoices.budget_id')
                                          ->join('invoice_concepts', 'invoices.id', '=', 'invoice_concepts.invoice_id')
                                          ->whereColumn('budgets.client_id', 'dominios.client_id')
                                          ->whereYear('invoice_concepts.created_at', $request->añoSinFacturas)
                                          ->where(function ($q) {
                                              $q->where('invoice_concepts.title', 'like', '%dominio%')
                                                ->orWhere('invoice_concepts.concept', 'like', '%dominio%')
                                                ->orWhere('invoice_concepts.title', 'like', '%Dominio%')
                                                ->orWhere('invoice_concepts.concept', 'like', '%Dominio%')
                                                ->orWhere('invoice_concepts.title', 'like', '%DOMINIO%')
                                                ->orWhere('invoice_concepts.concept', 'like', '%DOMINIO%');
                                          });
                              });
                    })
                    ->when($request->filtroFacturacion === 'facturado', function ($query) use ($añoActual) {
                        $query->whereExists(function ($subQuery) use ($añoActual) {
                            $subQuery->select(DB::raw(1))
                                    ->from('invoices')
                                    ->join('invoice_concepts', 'invoices.id', '=', 'invoice_concepts.invoice_id')
                                    ->whereColumn('invoices.client_id', 'dominios.client_id')
                                    ->where(function($q) use ($añoActual) {
                                        $q->where('invoice_concepts.title', 'like', '%' . $añoActual . '%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%' . $añoActual . '%')
                                          ->orWhere('invoice_concepts.title', 'like', '%renovación%')
                                          ->orWhere('invoice_concepts.title', 'like', '%renovacion%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%renovación%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%renovacion%')
                                          ->orWhere('invoice_concepts.title', 'like', '%dominio%')
                                          ->orWhere('invoice_concepts.title', 'like', '%Dominio%')
                                          ->orWhere('invoice_concepts.title', 'like', '%DOMINIO%')
                                          ->orWhere('invoice_concepts.title', 'like', '%anual%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%dominio%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%Dominio%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%DOMINIO%');
                                    });
                        });
                    })
                    ->when($request->filtroFacturacion === 'pendiente', function ($query) use ($añoActual) {
                        $query->whereNotExists(function ($subQuery) use ($añoActual) {
                            $subQuery->select(DB::raw(1))
                                    ->from('invoices')
                                    ->join('invoice_concepts', 'invoices.id', '=', 'invoice_concepts.invoice_id')
                                    ->whereColumn('invoices.client_id', 'dominios.client_id')
                                    ->where(function($q) use ($añoActual) {
                                        $q->where('invoice_concepts.title', 'like', '%' . $añoActual . '%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%' . $añoActual . '%')
                                          ->orWhere('invoice_concepts.title', 'like', '%renovación%')
                                          ->orWhere('invoice_concepts.title', 'like', '%renovacion%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%renovación%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%renovacion%')
                                          ->orWhere('invoice_concepts.title', 'like', '%dominio%')
                                          ->orWhere('invoice_concepts.title', 'like', '%Dominio%')
                                          ->orWhere('invoice_concepts.title', 'like', '%DOMINIO%')
                                          ->orWhere('invoice_concepts.title', 'like', '%anual%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%dominio%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%Dominio%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%DOMINIO%');
                                    });
                        });
                    });

            // Obtener dominios filtrados
            $dominios = $query->get();

            // Extraer clientes únicos con teléfono
            $clientesMap = [];

            foreach ($dominios as $dominio) {
                if ($dominio->cliente && !empty($dominio->cliente->phone)) {
                    $clienteId = $dominio->cliente->id;

                    // Evitar duplicados
                    if (!isset($clientesMap[$clienteId])) {
                        $clientesMap[$clienteId] = [
                            'id' => $clienteId,
                            'nombre' => trim($dominio->cliente->name . ' ' .
                                          ($dominio->cliente->primerApellido ?? '') . ' ' .
                                          ($dominio->cliente->segundoApellido ?? '')),
                            'telefono' => $dominio->cliente->phone
                        ];
                    }
                }
            }

            $clientes = array_values($clientesMap);

            \Illuminate\Support\Facades\Log::info('Clientes de dominios con teléfono encontrados:', [
                'total_dominios' => $dominios->count(),
                'clientes_unicos' => count($clientes)
            ]);

            return response()->json([
                'success' => true,
                'total' => count($clientes),
                'clientes' => $clientes
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en obtenerTelefonosClientesDominios:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
