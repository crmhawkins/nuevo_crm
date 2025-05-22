<?php

namespace App\Http\Controllers\CrmActivities;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Clients\Client;
use App\Models\Contacts\Contact;
use App\Models\Other\CivilStatus;
use App\Models\Users\User;
use App\Models\CrmActivities\CrmActivitiesMeetings;
use App\Models\CrmActivities\CrmActivitiesMeetingsComments;
use App\Models\CrmActivities\CrmActivitiesMeetingsXUsers;
use App\Models\Other\ContactBy;
use App\Models\Notes\Notes;
use Carbon\Carbon;
use App\Models\Alerts\Alert;
use App\Models\Alerts\AlertStatus;
use App\Mail\MailMeeting;
use Illuminate\Support\Facades\Mail;
use \stdClass;
use App\Classes\Notifications;
use App\Jobs\ProcessMeetingTranscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use CloudConvert\CloudConvert;
use CloudConvert\Models\Job;
use CloudConvert\Models\Task;
use App\Mail\MailNotification;
use App\Models\CrmActivities\CrmActivitiesMeetingsXContact;

class CrmActivityMeetingController extends Controller
{

    public function createClientMeeting(Client $client)
    {
        $contactBy = ContactBy::all();

        return view('admin.crm_activities.createClientMeeting', compact('client', 'contactBy'));
    }


    public function viewMeeting($id){

        $meeting = CrmActivitiesMeetings::find($id);
        $contactBy = ContactBy::all();

        $comments = CrmActivitiesMeetingsComments::where('meeting_id', $meeting->id)->orderBy('id', 'DESC')->get();

        if($meeting->files){
            $meeting->files = json_decode($meeting->files);
        }

        return view('crm_activities.meeting.show', compact('meeting', 'contactBy', 'comments'));
    }

    public function updateMeeting(Request $request){
        $data = $request->validate([
                    'id' => 'required',
                    'description' => 'required',
                ]);
        $meeting = CrmActivitiesMeetings::find($data['id']);
        if (!$meeting) {
            return response()->json(['message' => 'Acta no encontrada'], 404);
        }
        $meeting->update([
            'description' =>  $data['description']
        ]);
        return response()->json(['message' => 'Acta actualizada correctamente'], 200);
    }

    public function index(){
        $arrayMeetings = array();

        if(Auth::user()->admin_user_department_id == 1){
            $meetings = CrmActivitiesMeetingsXUsers::orderBy('id', 'DESC')->get();
        }
        else{
            $meetings = CrmActivitiesMeetingsXUsers::where('admin_user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        }

        foreach ($meetings as $meeting) {
            $arrayMeetings[] = $meeting->meeting;
        }

        return view('crm_activities.meeting.index', compact('arrayMeetings'));
    }

    public function addCommentsToMeeting($id, Request $request)
    {
        // Buscar la reunión por ID
        $meeting = CrmActivitiesMeetings::find($id);
        // Validar que la reunión exista
        if (!$meeting) {
            return response()->json(['error' => 'Reunión no encontrada.'], 404);
        }
        // Crear el comentario asociado a la reunión
        $comment = CrmActivitiesMeetingsComments::create([
            'admin_user_id' => Auth::user()->id,
            'meeting_id' => $meeting->id,
            'description' => $request->texto,
        ]);
        // Crear una alerta asociada al comentario (opcional)
        $dataAlert = [
            "admin_user_id" => $meeting->admin_user_id,
            "stage_id" => 15,
            "activation_datetime" => Carbon::now(),
            "status_id" => AlertStatus::ALERT_STATUS_PENDING,
            "reference_id" => $meeting->id,
            "cont_postpone" => 0,
            "description" => 'Han realizado un comentario en tu acta ' . $meeting->subject,
        ];
        $alert = Alert::create($dataAlert); // Descomentarlo si se necesita crear la alerta
        // Preparar y enviar la notificación por correo electrónico
        $mailNotif = new \stdClass();
        $mailNotif->title = "Tienes un comentario de " . $comment->adminUser->name . " en el acta " . $meeting->subject;
        $mailNotif->subject = "[CRMHAWKINS] Tienes un nuevo comentario en un acta";
        $mailNotif->description = "El comentario: " . $comment->description;
        $email = new MailNotification($mailNotif);

        Mail::to($meeting->adminUser->email)->send($email);

        // Devolver una respuesta JSON con los detalles del comentario
        return response()->json(['message' => 'Comentario agregado correctamente.', 'comment' => $comment]);
    }

    public function alreadyRead($id){

        $meeting = CrmActivitiesMeetings::find($id);

        $alert = Alert::where('stage_id', 29)->where('reference_id', $meeting->id)->get()->first();

        if($alert){
            $alert->status_id = 2;
            $alert->save();
        }

        // Respuesta
        return redirect()->route('reunion.index')->with(
            'toast', [
              'icon' => 'success',
              'mensaje' => 'Acta leida correctamente'
          ]);
    }





    public function createMeetingFromAllUsers(){
        $usuariosActa = User::where('inactive', 0)->whereNotIn('access_level_id',[1,7,8])->get();
        $usuarios = User::where('inactive', 0)->whereNotIn('access_level_id',[7,8])->get();

        if(Auth::user()->access_level_id == 6){
            $clients = Client::where('admin_user_id', Auth::id())->get();
        }else{
            $clients = Client::where('is_client', 1)->get();
        }

        $contactBy = ContactBy::all();

        return view('crm_activities.meeting.create', compact('clients', 'usuarios', 'usuariosActa', 'contactBy'));
    }


    public function getContactsFromClients(Request $request){
        $contacts = Client::find($request->id)->contacts;

        return response()->json($contacts);
    }

    public function register(Request $request){
        $request->validate([
            'client_id' => 'required',
            'contacts' => 'required',
            'date' => 'required',
            'time_start' => 'required',
            'contact_by_id' => 'required',
        ]);
    }

    public function storeMeetingFromAllUsers(Request $request){

        $images_path = array();

        $request->validate([
            'date' => 'required',
            'subject' => 'required',
            'files.*' => 'mimes:doc,pdf,docx,txt,zip,jpeg,jpg,png|size:20000',
            'audio' => 'nullable',  // Validación para el archivo de audio
        ]);

        if ($request->hasFile('archivos')) {
            $files = $request->file('archivos');
            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $path = $file->storeAs('', $filename, 'archivos');
                $images_path[] = $filename;
            }
        }

        $data = [
            "admin_user_id" => Auth::user()->id,
            "client_id" => $request->client_id,
            "contact_by_id" => $request->contact_by_id,
            "subject" => $request->subject,
            "description" => $request->description,
            "done" => $request->done,
            "date" => $request->date,
            "time_start" => $request->time_start,
            "time_end" => $request->time_end,
        ];

        if ($images_path) {
            $data['files'] = json_encode($images_path);
        }

        if (!isset($data['done'])) {
            $data['done'] = 0;
        }

        if (isset($data['date']) && $data['date'] != null) {
            $data['date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['date'])));
        }

        // Guardar la reunión
        $meeting = CrmActivitiesMeetings::create($data);
        $meeting->save();

        $fileCount = 0;  // Inicializar el contador de archivos

        if ($request->has('audio_filenames')) {
            foreach ($request->audio_filenames as $tempFilename) {
                // Ruta del archivo temporal
                $tempPath = storage_path('app/public/meetings/audios/temp/' . $tempFilename);
                if (file_exists($tempPath)) {
                    // Generar un nuevo nombre de archivo con el ID de la reunión y un número incremental de dos cifras
                    $incrementalNumber = str_pad(++$fileCount, 2, '0', STR_PAD_LEFT);
                    $newFilename = $meeting->id . '_' . $incrementalNumber . '.mp3';
                    // Nueva ruta
                    $newPath = storage_path('app/public/meetings/audios/' . $newFilename);
                    // Mover el archivo
                    rename($tempPath, $newPath);
                }
            }
        }

      // Manejo del archivo de audio
        // if ($request->hasFile('audio')) {
        //     $audioFile = $request->file('audio');
        //     $incrementalNumber = str_pad(++$fileCount, 2, '0', STR_PAD_LEFT);
        //     $audioFilename = $meeting->id . '_'. $incrementalNumber .'.' . $audioFile->getClientOriginalExtension();
        //     // Guardar el archivo en el almacenamiento
        //     $audioPath = $audioFile->storeAs('public/reuniones', $audioFilename);
        //     // Generar la URL pública del archivo guardado
        //     $audioUrl = storage_path('app/public/reuniones/' . $audioFilename);
        // }

        // Guardar los datos relacionados con el equipo, contactos, etc. (esto se mantiene igual)
        if ($request->has('teamActa')) {
            foreach ($request->teamActa as $team) {
                $usuario = User::find($team);
                CrmActivitiesMeetingsXUsers::create([
                    "admin_user_id" => $usuario->id,
                    "meeting_id" => $meeting->id,
                    "team" => 1,
                ]);
            }
        }

        if ($request->has('contacts')) {
            foreach ($request->contacts as $contact) {
                $usuario = Contact::find($contact);
                if ($usuario) {
                    CrmActivitiesMeetingsXContact::create([
                        "admin_user_id" => $usuario->id,
                        "meeting_id" => $meeting->id,
                    ]);
                }
            }
        }

        if ($request->has('contact_emails')) {
            foreach ($request->contact_emails as $index => $contact) {
                if (!empty($contact)) {
                    $name = $request->contact_names[$index];

                    $newContacto = array();
                    $newContacto['admin_user_id'] = Auth::user()->id;
                    $newContacto['civil_status_id'] = null;
                    $newContacto['phone'] = null;
                    $newContacto['email'] = $contact;
                    $newContacto['client_id'] = $request->client_id;
                    $newContacto['name'] = $name;
                    $newContacto['privacy_policy_accepted'] = false;
                    $newContacto['cookies_accepted'] = false;
                    $newContacto['newsletters_sending_accepted'] = false;
                    $contacto = Contact::create($newContacto);

                    CrmActivitiesMeetingsXContact::create([
                        "admin_user_id" => $contacto->id,
                        "meeting_id" => $meeting->id,
                    ]);
                }
            }
        }


        if ($request->has('team')) {
            foreach ($request->team as $user) {
                $usuario = User::find($user);
                if ($usuario) {
                    CrmActivitiesMeetingsXUsers::create([
                        "admin_user_id" => $usuario->id,
                        "meeting_id" => $meeting->id,
                        "team" => 2,
                    ]);
                }
            }
        }
        if(isset($audioUrl)){
            ProcessMeetingTranscription::dispatch($meeting, $audioUrl);
        }

        return redirect()->route('reunion.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Se creó un acta de reunión correctamente, ahora puedes enviar el correo manualmente.'
        ]);
    }

    public function storeAudio(Request $request){
        $audioFile = $request->file('audio');
        // Generar un UUID para el nombre del archivo temporal
        $uuid = (string) \Illuminate\Support\Str::uuid();
        $audioFilename = 'Temp_' . uniqid() . '.' . $audioFile->getClientOriginalExtension();
        // Guardar el archivo en el almacenamiento temporal
        $audioFile->storeAs('public/meetings/audios/temp', $audioFilename);
        $audioUrl = asset('storage/meetings/audios/temp/' . $audioFilename);
        return response()->json(['audio_url' => $audioUrl, 'audio_filename' => $audioFilename]);
    }




    public function sendMeetingEmails(Request $request){
        $userEmails = array();
        $userNames = array();
        $userAsistente = array();

        $meeting = CrmActivitiesMeetings::find($request->meeting_id);

        if (!$meeting || !$meeting->description) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No se puede enviar el correo. La descripción no está presente.'
            ]);
        }

        $meetingByuser1 = CrmActivitiesMeetingsXUsers::where('meeting_id', $meeting->id)->where('team',1)->get();

        foreach ($meetingByuser1 as $team) {

            $usuario = User::find($team->admin_user_id);

            $dataAlert = [
                "admin_user_id" => $usuario->id,
                "stage_id" => 29,
                "activation_datetime" => Carbon::now(),
                "status_id" => AlertStatus::ALERT_STATUS_PENDING,
                "reference_id" => $meeting->id,
                "cont_postpone" => 0,
                "description" => 'Nueva acta de reunion creada',
            ];

            $mailNotif = new \stdClass();
            $mailNotif->title = "Tienes una nueva acta de reunion";
            $mailNotif->subject = "[CRMHAWKINS]Tienes una nueva acta de reunion";
            $mailNotif->description = $meeting->description;

            $email = new MailNotification($mailNotif);

            Mail::to($usuario->email)->send($email);

            $alert = Alert::create($dataAlert);
            $alert->save();
        }

        $meetingByContact = CrmActivitiesMeetingsXContact::where('meeting_id', $meeting->id)->get();

        if(isset($meetingByContact)){
            foreach ($meetingByContact as $contact) {
                $usuario = Contact::find($contact->contact_id);
                if($usuario){
                    $userEmails[] = $usuario->email;
                    $userNames[] = $usuario->name;
                }
            }
        }

        $meetingByuser2 = CrmActivitiesMeetingsXUsers::where('meeting_id', $meeting->id)->where('team',2)->get();

        foreach ($meetingByuser2 as $user) {
            $user = User::find($user->admin_user_id);
            if($user){
                $userAsistente[] = $user->name;
            }
        }

        $client = Client::find($meeting->client_id);
        $modalidad = ContactBy::find($meeting->contact_by_id);

        $meetingObject = new \stdClass();
        $meetingObject->subject = $meeting->subject;
        $meetingObject->description = $meeting->description;
        $meetingObject->modalidad = $modalidad;
        $meetingObject->date = $meeting->date;
        $meetingObject->client_name = $client->name;
        if($client->city){
            $meetingObject->city = $client->city;
        }
        $meetingObject->contacts = $userNames;
        $meetingObject->asistentes = $userAsistente;
        $meetingObject->time_start = $meeting->time_start;
        $meetingObject->time_end = $meeting->time_end;
        $email = new MailMeeting($meetingObject);

        if(!empty($userEmails)){

            Mail::to($userEmails)
            ->cc(Auth::user()->email)
            ->send($email);
        }

        if (!empty($userEmails)) {
            Mail::to($userEmails)
                ->cc(Auth::user()->email)
                ->send($email);

            return redirect()->back()->with('toast', [
                'icon' => 'success',
                'mensaje' => 'Correo enviado correctamente.'
            ]);
        }

        return redirect()->back()->with('toast', [
            'icon' => 'error',
            'mensaje' => 'No se encontraron correos electrónicos para enviar.'
        ]);
    }

    public function transcripcion($audio)
    {
        $token = env('OPENAI_API_KEY');
        $url = 'https://api.openai.com/v1/audio/transcriptions';


        // Headers necesarios
        $headers = array(
            'Authorization: Bearer ' . $token
        );

        // Datos para la solicitud a Whisper
        $data = array(
            'file' => curl_file_create($audio), // Crear el archivo para CURL
            'model' => 'whisper-1', // Modelo de Whisper
        );

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Enviar datos como multipart/form-data
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Verbose para depuración detallada
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        $verbose = fopen('curl_verbose.log', 'w');
        curl_setopt($curl, CURLOPT_STDERR, $verbose);

        // Establecer tiempos de espera para evitar el error de operación abortada
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);  // 5 minutos de timeout
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300);  // 5 minutos para conexión

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);

        // Verificar si hubo errores
        if (curl_errno($curl)) {
            return 'Error: ' . curl_error($curl);
        }

        curl_close($curl);

        // Decodificar la respuesta JSON
        $response_data = json_decode($response, true);

        return $response_data;
    }

    public function chatgpt($texto){

        $token = env('OPENAI_API_KEY');

        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        );

        // Construir el contenido del mensaje que incluye la imagen en base64, paises y tipos de documento como texto
        $data = array(
            "model" => "gpt-4",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "Analiza esta transcripción de una reunión del equipo Hawkins, que puede involucrar tanto a miembros internos como a clientes. Elabora un resumen conciso que destaque únicamente los temas discutidos y los puntos clave mencionados durante la reunión. Asegúrate de excluir cualquier información confidencial, sensible o relacionada con temas ilegales. El resumen debe ser claro, preciso y enfocado únicamente en los aspectos relevantes de la conversación."
                        ],
                        [
                            "type" => "text",
                            "text" => "Texto de la reunion: " . $texto
                        ],
                    ]
                ]
            ]
        );

        // Inicializar cURL y configurar las opciones
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($curl);
        curl_close($curl);

        // Decodificar la respuesta JSON
        $response_data = json_decode($response, true);

        return $response_data;
    }

    public function dividirAudioPorTamaño($filePath, $outputDirectory, $maxSizeMB = 25)
    {
        // Asegúrate de que la carpeta de salida exista
        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0755, true);
        }

        // Obtener el bitrate del archivo de audio usando ffprobe
        $bitrate = shell_exec("ffprobe -v error -select_streams a:0 -show_entries stream=bit_rate -of default=noprint_wrappers=1:nokey=1 {$filePath}");

        // Quitar los saltos de línea y espacios del bitrate y asegurarse de que sea numérico
        $bitrate = trim($bitrate);

        // Verificar si el bitrate no es un número o está vacío
        if (!is_numeric($bitrate) || empty($bitrate)) {
            $bitrate = 128 * 1000; // Valor por defecto en bits (128 kbps en bps)
        }

        // Convertir el bitrate a entero para asegurarnos de que sea un número válido
        $bitrate = (int) $bitrate;

        // Convertir el tamaño máximo permitido en bits
        $maxSizeBits = ($maxSizeMB * 8 * 1024 * 1024); // 25 MB en bits

        // Calcular la duración del segmento en segundos basándonos en el tamaño y el bitrate
        $segmentDuration = round($maxSizeBits / $bitrate); // Duración aproximada del segmento en segundos

        // Nombre base del archivo
        $baseName = pathinfo($filePath, PATHINFO_FILENAME);

        // Comando de FFmpeg para dividir el archivo en segmentos y recodificar a MP3
        $command = "ffmpeg -i {$filePath} -f segment -segment_time {$segmentDuration} -c:a libmp3lame -b:a 128k {$outputDirectory}/{$baseName}_part%d.mp3";

        // Ejecutar el comando
        shell_exec($command);

        // Obtener los archivos generados
        $files = glob("{$outputDirectory}/{$baseName}_part*.mp3");
        return $files;
    }

}
