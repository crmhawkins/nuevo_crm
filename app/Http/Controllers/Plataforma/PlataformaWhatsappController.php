<?php

namespace App\Http\Controllers\Plataforma;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Plataforma\WhatsappAlerts;
use App\Models\Plataforma\WhatsappConfig;
use App\Models\Plataforma\WhatsappLog;
use App\Models\Plataforma\WhatsappCatId;
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

        $respuestas = [];
        $campanias = [['nombre' => 'Campaña 1', 'estado' => 'Aceptada'], ['nombre' => 'Campaña 2', 'estado' => 'Rechazada'], ['nombre' => 'Campaña 3', 'estado' => 'Enviada']];

        return view('plataforma.dashboard', compact('client', 'alerts', 'respuestas', 'campanias'));
    }

    public function deleteAlert(Request $request) {
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

        $clients = Client::select('id', 'name', 'phone')->whereNotNull('phone')->where('phone', '!=', '')->get();
        return view('plataforma.campanias', compact('user', 'campanias', 'templates', 'clients'));
    }

    public function createCampania()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $request = request();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        // Convertir el HTML a formato compatible con WhatsApp
        $mensaje = $this->convertHtmlToWhatsappFormat($validated['mensaje']);

        // Crear la campaña
        $campania = CampaniasWhatsapp::create([
            'nombre' => $validated['nombre'],
            'mensaje' => $mensaje,
            'estado' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaña creada exitosamente',
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
        ]);

        // Convertir el HTML a formato compatible con WhatsApp
        $mensaje = $this->convertHtmlToWhatsappFormat($validated['mensaje']);

        // Crear la campaña
        $campania = CampaniasWhatsapp::create([
            'nombre' => $validated['nombre'],
            'mensaje' => $mensaje,
            'estado' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaña creada exitosamente',
            'campania' => $campania,
        ]);
    }

    // Función para convertir HTML a formato WhatsApp
    private function convertHtmlToWhatsappFormat($html)
    {
        // Convertir etiquetas de HTML a WhatsApp
        $html = preg_replace('/<b>(.*?)<\/b>/', '*$1*', $html); // Negrita
        $html = preg_replace('/<i>(.*?)<\/i>/', '_$1_', $html); // Cursiva
        $html = preg_replace('/<u>(.*?)<\/u>/', '$1', $html); // Subrayado no soportado por WhatsApp, lo eliminamos

        $html = str_replace('<br>', "\n", $html);

        // Limpiar el HTML residual
        $html = strip_tags($html);

        return $html;
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

        return view('plataforma.templates', compact('user', 'templates'));
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

            // Send messages to each client
            foreach ($clientes as $clientId) {
                $client = Client::find($clientId);
                if ($client && $client->phone) {
                    // Uncomment when ready to send actual messages
                    // $this->sendWhatsappMessage($client, $campania->mensaje);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Campaña enviada exitosamente',
                'type' => 'success',
                'data' => [
                    'estado' => $campania->estado,
                    'fecha_lanzamiento' => $campania->fecha_lanzamiento->format('d-m-Y H:i:s'),
                ],
            ]);
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
        $logs = WhatsappLog::paginate(20);
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

    public function configuracionStore(Request $request) {
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



    public function saveLog(Request $request) {

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
}
