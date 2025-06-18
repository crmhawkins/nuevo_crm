<?php

namespace App\Http\Controllers\Plataforma;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Plataforma\MensajesPendientes;
use App\Models\Plataforma\ModeloMensajes;
use App\Models\Plataforma\PlataformaTemplates;
use App\Models\Plataforma\WhatsappAlerts;
use App\Models\Plataforma\WhatsappConfig;
use App\Models\Plataforma\WhatsappLog;
use App\Models\Plataforma\WhatsappCatId;
use App\Models\Plataforma\WhatsappMessages;
use Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Plataforma\WhatsappContacts;
use App\Models\Plataforma\CampaniasWhatsapp;
use App\Http\Controllers\Plataforma\PlataformaWhatsappApi;

class PlataformaWhatsappController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $client = Auth::user();
        $contacts = WhatsappContacts::with('cliente')->paginate(20);
        $clients = Client::select('id', 'name', 'phone')->whereNotNull('phone')->where('phone', '!=', '')->paginate(20);

        $alerts = WhatsappAlerts::where('status', 0)->get();

        // Estadísticas para el gráfico
        $stats = [
            'enviados' => WhatsappMessages::where('status', 1)->count(),
            'leidos' => WhatsappMessages::where('status', 2)->count(),
            'recibidos' => WhatsappMessages::where('status', 3)->count(),
            'pendientes' => MensajesPendientes::where('status', 0)->count(),
            'total' => WhatsappMessages::count(),
        ];

        $respuestas = [];
        $campanias = [['nombre' => 'Campaña 1', 'estado' => 'Aceptada'], ['nombre' => 'Campaña 2', 'estado' => 'Rechazada'], ['nombre' => 'Campaña 3', 'estado' => 'Enviada']];

        $templates = PlataformaTemplates::all();
        return view('plataforma.dashboard', compact('client', 'alerts', 'respuestas', 'campanias', 'stats'));
    }

    public function deleteAlert(Request $request)
    {
        $alert = WhatsappAlerts::find($request->id);
        $alert->status = 1;
        $alert->save();
        return response()->json(['success' => true]);
    }

    public function campanias()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $campanias = CampaniasWhatsapp::paginate(20);
        $templates = PlataformaTemplates::all();
        $clients = Client::select('id', 'name', 'phone')->whereNotNull('phone')->where('phone', '!=', '')->orderBy('name', 'asc')->get();
        $whatsapp_clients = WhatsappContacts::select('wid', 'name', 'phone')->orderBy('name')->get();

        return view('plataforma.campanias', compact('user', 'campanias', 'templates', 'clients', 'whatsapp_clients'));
    }

    public function createCampania(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'plantilla' => 'required|integer',
            'clientes' => 'required|array',
        ]);

        // Crear lista de clientes según el tipo de ID
        $clientes = [];
        foreach ($validated['clientes'] as $clienteId) {
            $w = 2;
            // Verificar si el ID empieza con 'W' para determinar si es de la tabla whatsapp_contacts
            if (str_starts_with($clienteId, 'W')) {
                $id = substr($clienteId, 1); // Quitar el prefijo 'W'
                $cliente = WhatsappContacts::find($id); // Buscar en la tabla whatsapp_contacts
                $w = 1;
            } else {
                $cliente = Client::find($clienteId); // Buscar en la tabla clients
                $w = 0;
            }

            if ($cliente && $w == 1) {
                $clientes[] = $cliente->wid;
            } else if ($cliente && $w == 0) {
                $clientes[] = $cliente->id;
            }
        }

        $msg = PlataformaTemplates::find($validated['plantilla']);
        $mensaje = $this->convertHtmlToWhatsappFormat($msg->mensaje);

        // Crear campaña
        $campania = CampaniasWhatsapp::create([
            'nombre' => $validated['nombre'],
            'mensaje' => $mensaje,
            'estado' => 1,
            'clientes' => $clientes, // Clientes procesados
            'id_template' => $validated['plantilla'],
        ]);

        $campania->save();

        return response()->json([
            'success' => true,
            'message' => 'Campaña creada exitosamente. Se está procesando en segundo plano.',
            'campania' => $campania,
        ]);
    }

    public function createTemplate()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $request = request();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'mensaje' => 'required|string',

            'contenido' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,gif,svg,webp,mp 4,avi,mov',
            'botones' => 'nullable|string',
        ]);
        // Convertir el HTML a formato compatible con WhatsApp
        $mensaje = $this->convertHtmlToWhatsappFormat($validated['mensaje']);

        $validated['mensaje'] = $mensaje;
        $validated['botones'] = [];
        // Crear la campaña
        $plantilla = PlataformaTemplates::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plantilla creada exitosamente',
            'plantilla' => $plantilla,
        ]);
    }

    private function convertHtmlToWhatsappFormat($html)
    {
        // Convertir HTML a WhatsApp Format

        // 1. Negrita y cursiva
        $html = preg_replace('/<b>(.*?)<\/b>/i', '*$1*', $html);
        $html = preg_replace('/<strong>(.*?)<\/strong>/i', '*$1*', $html);
        $html = preg_replace('/<i>(.*?)<\/i>/i', '_$1_', $html);
        $html = preg_replace('/<em>(.*?)<\/em>/i', '_$1_', $html);

        // 2. Eliminar subrayado
        $html = preg_replace('/<u>(.*?)<\/u>/i', '$1', $html);

        // 3. Convertir <br> y </p> a saltos reales
        $html = str_ireplace(['<br>', '<br/>', '<br />'], "\n", $html);
        $html = preg_replace('/<\/p>/i', "\n\n", $html);
        $html = preg_replace('/<p[^>]*>/i', '', $html);

        // 4. Convertir entidades HTML comunes (opcional)
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5);

        // 5. Eliminar etiquetas restantes
        $html = strip_tags($html);

        // 6. Preservar saltos de línea reales (ya incluidos o manuales)
        $html = preg_replace("/\r\n|\r/", "\n", $html); // Normaliza
        $html = preg_replace("/\n{3,}/", "\n\n", $html); // Máx 2 líneas seguidas

        // 7. Trim
        return trim($html);
    }

    public function clientes(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        $clients = Client::whereNotNull('phone')->where('phone', '!=', '')->paginate(20);

        return view('plataforma.clientes', compact('user', 'clients'));
    }

    // Templates

    public function templates()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $url = 'https://graph.facebook.com/v22.0/262465576940163/message_templates?fields=name,status';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . env('WHATSAPP_TOKEN'), 'Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $templates = json_decode($response)->data;
        } else {
            $templates = [];
        }

        // Get templates from db
        $templates = PlataformaTemplates::whereNot('status', 3)->get();

        return view('plataforma.templates', compact('user', 'templates'));
    }

    public function deleteTemplate(Request $request)
    {
        $template = PlataformaTemplates::find($request->id);
        $template->status = 3;
        $template->save();
        return response()->json(['success' => true], 200);
    }

    public function sendCampania(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Usuario no autenticado',
                    'type' => 'error',
                ],
                401,
            );
        }

        try {
            $campania_id = $request->input('campania_id');

            if (!$campania_id) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de campaña no proporcionado',
                        'type' => 'error',
                    ],
                    400,
                );
            }

            $campania = CampaniasWhatsapp::find($campania_id);

            if (!$campania) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Campaña no encontrada',
                        'type' => 'error',
                    ],
                    404,
                );
            }

            // Get clients array from campaign
            $clientes = $campania->clientes;

            if (empty($clientes)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'La campaña no tiene clientes asignados',
                        'type' => 'error',
                    ],
                    400,
                );
            }

            // Update campaign status to sent (3)
            $campania->estado = 3;
            $campania->fecha_lanzamiento = now();
            $campania->save();

            $log = new WhatsappLog();
            $log->type = 1;
            $log->clients = $clientes;
            $log->id_campania = $campania->id;
            $log->message = $campania->mensaje;
            $log->id_template = $campania->id_template;
            $log->save();

            // Send messages to each client
            foreach ($clientes as $clientId) {
                $client = Client::find($clientId);
                if (!$client) {
                    continue;
                }

                $phone = $client->phone;
                if (str_starts_with($phone, '34')) {
                    $phone = substr($phone, 2);
                }
                $phone = str_replace([' ', '+'], '', $phone);

                // Skip invalid phone numbers
                if (empty($phone) || $phone[0] === '9' || $phone[0] === '8') {
                    continue;
                }

                $phone = '34' . $phone;

                $mensaje = $this->replaceTemplateVariables($campania->mensaje, $client);

                // Create pending message
                $mensajePendiente = new MensajesPendientes();
                $mensajePendiente->tlf = $phone;
                $mensajePendiente->message = $mensaje;
                $mensajePendiente->save();

                // Log the pending message
                $log = new WhatsappLog();
                $log->clients = $client->id;
                $log->id_campania = $campania->id;
                $log->message = 'Mensaje pendiente de envío';
                $log->save();
            }
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Campaña enviada exitosamente y mensajes puestos en cola para ' . count($clientes) . ' clientes',
                    'type' => 'success',
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al enviar la campaña: ' . $e->getMessage(),
                    'type' => 'error',
                ],
                500,
            );
        }
    }

    public function logs()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $logs = WhatsappLog::orderBy('id', 'desc')->paginate(20);
        return view('plataforma.logs', compact('user', 'logs'));
    }

    public function logsClient(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        $clientIds = $request->input('client_id');
        $clients = Client::whereIn('id', $clientIds)->pluck('name')->toArray();
        $campaniaId = $request->input('campania_id');
        $categoriaCliente = null;

        if ($campaniaId) {
            $campania = CampaniasWhatsapp::find($campaniaId);
            if ($campania) {
                $categoriaCliente = $campania->categoria_cliente;
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Logs obtenidos exitosamente',
            'type' => 'success',
            'data' => [
                'clients' => $clients,
                'categoria' => $categoriaCliente,
            ],
        ]);
    }

    public function configuracion()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $client = Auth::user();

        $config = WhatsappConfig::first();
        $categorias = WhatsappCatId::all();
        return view('plataforma.configuracion', compact('client', 'config', 'categorias'));
    }

    public function configuracionStore(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'company_apikey' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_cat_id' => 'nullable|integer',
            'company_web' => 'nullable|string',
            'company_address' => 'nullable|string',
            'company_mail' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        if ($request->hasFile('company_logo')) {
            $logo = $request->file('company_logo');
            $logoName = time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/whatsapp'), $logoName);
            $validated['company_logo'] = 'uploads/whatsapp/' . $logoName;
        }

        $config = WhatsappConfig::first();

        if ($validated['company_apikey'] == '*****************' || $validated['company_apikey'] == '') {
            unset($validated['company_apikey']);
        }

        if (!$config) {
            $config = new WhatsappConfig();
            $config->fill($validated);
            $config->save();
        } else {
            $config->fill($validated);
            $config->save();
        }

        return redirect()->route('plataforma.configuracion')->with('toast', 'Configuración actualizada correctamente');
    }

    public function programarCampania(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        try {
            $campaniaId = $request->input('campania_id');
            $fecha = $request->input('fecha');
            $hora = $request->input('hora');

            $campania = CampaniasWhatsapp::find($campaniaId);
            $campania->fecha_programada = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $fecha . ' ' . $hora);
            $campania->save();

            return response()->json([
                'success' => true,
                'message' => 'Campaña programada exitosamente',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al programar la campaña: ' . $e->getMessage(),
                    'type' => 'error',
                ],
                500,
            );
        }
    }

    public function saveLog(Request $request)
    {
        // Tipos de estado:
        // 1 - Enviado
        // 2 - No enviado
        // 3 - Respuesta recibida
        // 4 - Error
        // 5 - Desconocido

        $validated = $request->validate([
            'type' => 'required|integer|in:1,2,3,4,5',
            'message' => 'nullable|string',
            'response' => 'nullable|string',
            'client_category' => 'nullable|string',
            'id_campania' => 'nullable|string',
            'id_template' => 'nullable|string',
        ]);

        // Add to whatsapp_log table

        $log = new WhatsappLog();
        $log->type = $validated['type'];
        $log->message = $validated['message'];
        $log->response = $validated['response'];
        $log->id_campania = $validated['id_campania'];
        $log->id_template = $validated['id_template'];
        $log->save();
    }

    public function storeLog(Request $request)
    {
        $log = new WhatsappLog();
        $log->fill($request->all());
        $log->save();
        return response()->json([
            'success' => true,
            'message' => 'Log almacenado exitosamente',
            'type' => 'success',
        ]);
    }

    public function chat()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        return view('plataforma.chat', compact('user'));
    }

    public function getContact($contactId)
    {
        $contact = WhatsappContacts::find($contactId);
        return response()->json($contact);
    }

    public function getMessages(Request $request)
    {
        $response = Http::get('http://whatsapp-api.hawkins.es:4688/get-chat', [
            'chatId' => $request->chatId,
        ]);

        return response()->json($response->json());
    }

    public function getChats()
    {
        $response = Http::get('http://whatsapp-api.hawkins.es:4688/get-chats');

        return response()->json($response->json());
    }

    private function replaceTemplateVariables($message, $client = null)
    {
        $variables = [
            '{cliente}' => $client ? $client->name : '',
            '{fecha}' => now()->format('d/m/Y'),
            '{telefono}' => $client ? $client->phone : '',
            '{email}' => $client ? $client->email : '',
            '{direccion}' => $client ? $client->address : '',
        ];

        return str_replace(array_keys($variables), array_values($variables), $message);
    }

    public function sendMessage(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $validated = $request->validate([
            'chatId' => 'required|string',
            'message' => 'required|string',
        ]);

        $cliente = Client::where('phone', $validated['chatId'])->first();

        if ($cliente) {
            $message = $this->replaceTemplateVariables($validated['message'], $cliente);
        } else {
            $message = $validated['message'];
        }

        $response = Http::post('http://whatsapp-api.hawkins.es:4688/send-message', [
            'message' => $message,
            'chatId' => $validated['chatId'],
        ]);

        return response()->json($response->json());
    }

    public function storeMsg(Request $request)
    {
        $msg = new WhatsappMessages();
        if (WhatsappMessages::where('message_id', $request->message_id)->exists()) {
            $msg = WhatsappMessages::where('message_id', $request->message_id)->first();
            $msg->status = $request->status;
            $msg->save();
        } else {
            $msg->fill($request->all());
            $msg->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Mensaje almacenado exitosamente',
            'type' => 'success',
        ]);
    }
}
