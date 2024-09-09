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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Mail\MailNotification;


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

        // $alert = Alert::create($dataAlert); // Descomentarlo si se necesita crear la alerta

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

        // $alert = Alert::where('stage_id', 29)->where('reference_id', $meeting->id)->get()->first();

        // if($alert){
        //     $alert->status_id = 2;
        //     $alert->save();
        // }

        // Respuesta
        return redirect()->route('reunion.index')->with(
            'toast', [
              'icon' => 'success',
              'mensaje' => 'Acta leida correctamente'
          ]);
    }


    // public function storeClientMeeting(Client $client, Request $request){
    //     // Validación
    //     $request->validate([
    //         'date' => 'required',
    //         'subject' => 'required',
    //     ]);

    //     // Formulario datos
    //     $data = $request->all();
    //     $data['admin_user_id'] = Auth::user()->id;
    //     $data['client_id'] = $client->id;

    //     // Booleans
    //     if(!isset($data['done'])){
    //         $data['done'] = 0;
    //     }else{
    //         $data['done'] = 1;
    //     }

    //     // Dates
    //     if(isset($data['date'])){
    //         if ($data['date'] != null){
    //             $data['date'] = date('Y-m-d', strtotime(str_replace('/', '-',  $data['date'])));
    //         }
    //     }

    //     // Guardar
    //     $crmActivityclientMeeting = CrmActivitiesMeetings::create($data);
    //     $crmActivityclientMeeting->save();

    //     // Respuesta
    //     return AjaxForm::custom([
    //         'message' => 'Llamada a cliente registrada',
    //         'entryUrl' => route('admin.crm_activity_meeting.editClientMeeting', $crmActivityclientMeeting->id),
    //     ])->jsonResponse();
    // }


    // public function editClientMeeting(CrmActivitiesMeetings $clientMeeting){
    //     $client = Client::where('id', $clientMeeting->client_id)->get()->first();
    //     $contactBy = ContactBy::all();

    //     if($clientMeeting->files){
    //         $clientMeeting->files = json_decode($clientMeeting->files);
    //     }

    //     return view('admin.crm_activities.editClientMeeting', compact('clientMeeting', 'client', 'contactBy'));
    // }


    // public function updateClientMeeting(Request $request, CrmActivitiesMeetings $clientMeeting){
    //     // Validación
    //     $request->validate([
    //         'date' => 'required',
    //         'subject' => 'required',
    //     ]);

    //     // Datos del formulario
    //     $data = $request->all();
    //     $data['admin_user_id'] = Auth::user()->id;

    //     // Booleans
    //     if(!isset($data['done'])){
    //         $data['done'] = 0;
    //     }else{
    //         $data['done'] = 1;
    //     }

    //     // Dates
    //     if(isset($data['date'])){
    //         if ($data['date'] != null){
    //             $data['date'] = date('Y-m-d', strtotime(str_replace('/', '-',  $data['date'])));
    //         }
    //     }

    //     // Actualizar
    //     $clientMeeting->fill($data);
    //     $clientMeeting->save();

    //     // Respuesta
    //     return AjaxForm::custom([
    //         'message' => 'La reunión se actualizó correctamente',
    //         'entryUrl' => route('admin.crm_activity_meeting.editClientMeeting', $clientMeeting->id),
    //     ])->jsonResponse();
    // }


    // public function destroyClientMeeting(CrmActivitiesMeetings $clientMeeting)
    // {
    //     try {

    //         if($clientMeeting->files){
    //             $clientMeeting->files = json_decode($clientMeeting->files);
    //             foreach ($clientMeeting->files as $image){
    //                 Storage::disk('archivos')->delete($image);
    //             }
    //         }
    //         //Borrar nota
    //         $deleted = $clientMeeting->delete();
    //         // Respuesta
    //         return AjaxForm::custom([
    //             'message' => 'La reunión se borró correctamente',
    //         ])->jsonResponse();
    //     } catch (\Exception $e) {
    //          // Respuesta
    //          return AjaxForm::custom([
    //             'message' => 'La reunión no pudo ser eliminada.Pruebe más tarde.',
    //             'entryUrl' => route('admin.crm_activity_meeting.editClientMeeting', $clientMeeting->id),
    //         ])->jsonResponse();
    //     }
    // }

    public function transcripcion(Request $request){
        if (!isset($request->id) || !isset($request->texto)) {
            return response()->json('Error falta el Id o el Texto', 400);
        }

        $id = $request->id;
        $texto = $request->texto;
        $acta = CrmActivitiesMeetings::find($id);
        if (!isset($acta)) {
            return response()->json('Error falta el Id no encuentra ninguna acta', 400);
        }
        $acta->description = $texto;
        $acta->save();
        return response()->json('Guardado Correctamente', 200);
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


    public function storeMeetingFromAllUsers(Request $request){
        $userEmails = array();
        $userNames = array();
        $userAsistente = array();
        $images_path = array();

        $request->validate([
            'date' => 'required',
            'subject' => 'required',
            'files.*' => 'mimes:doc,pdf,docx,txt,zip,jpeg,jpg,png|size:20000'
        ]);

        if($request->hasFile('archivos')) {
            $files = $request->file('archivos'); // Cambia a usar $request->file para obtener los archivos correctamente
            $images_path = [];

            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();

                // Almacena el archivo en el disco 'archivos' y guarda la ruta en $images_path
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

        if($images_path){
            $data['files'] = json_encode($images_path);
        }

        // Booleans
        if(!isset($data['done'])){
            $data['done'] = 0;
        }else{
            $data['done'] = 1;
        }

        // Dates
        if(isset($data['date'])){
            if ($data['date'] != null){
                $data['date'] = date('Y-m-d', strtotime(str_replace('/', '-',  $data['date'])));
            }
        }

        // Guardar
        $meeting = CrmActivitiesMeetings::create($data);
        $meeting->save();

        foreach ($request->teamActa as $team) {
            $usuario = User::find($team);
            $dataMeeting = [
                "admin_user_id" => $usuario->id,
                "meeting_id" => $meeting->id,
            ];

            $meetingByuser = CrmActivitiesMeetingsXUsers::create($dataMeeting);
            $meetingByuser->save();

            $dataAlert = [
                "admin_user_id" => $usuario->id,
                "stage_id" => 29,
                "activation_datetime" => Carbon::now(),
                "status_id" => AlertStatus::ALERT_STATUS_PENDING,
                "reference_id" => $meeting->id,
                "cont_postpone" => 0,
                "description" => 'Nueva acta de reunion creada',
            ];

            // if($usuario->device_token){
            //     $notification = new Notifications('Nueva acta de reunión', 'Tienes una nueva acta de reunión', $usuario->device_token);
            //     $notificationSent = $notification->create();
            // }

            $mailNotif = new \stdClass();
            $mailNotif->title = "Tienes una nueva acta de reunion";
            $mailNotif->subject = "[CRMHAWKINS]Tienes una nueva acta de reunion";
            $mailNotif->description = $request->description;

            $email = new MailNotification($mailNotif);

            Mail::to($usuario->email)->send($email);

            // $alert = Alert::create($dataAlert);
            // $alert->save();

        }

        if(isset($request->contacts)){
            foreach ($request->contacts as $contact) {
                $usuario = Contact::find($contact);
                if($usuario){
                    $userEmails[] = $usuario->email;
                    $userNames[] = $usuario->name;
                }
            }
        }

        foreach ($request->team as $user) {
            $user = User::find($user);
            if($user){
                $userAsistente[] = $user->name;
            }
        }

        $client = Client::find($request->client_id);
        $modalidad = ContactBy::find($request->contact_by_id);

        $meetingObject = new \stdClass();
        $meetingObject->subject = $request->subject;
        $meetingObject->description = $request->description;
        $meetingObject->modalidad = $modalidad;
        $meetingObject->date = $data['date'];
        $meetingObject->client_name = $client->name;
        if($client->city){
            $meetingObject->city = $client->city;
        }
        $meetingObject->contacts = $userNames;
        $meetingObject->asistentes = $userAsistente;
        $meetingObject->time_start = $request->time_start;
        $meetingObject->time_end = $request->time_end;

        $email = new MailMeeting($meetingObject);

        if(!empty($userEmails)){

            Mail::to($userEmails)
            ->cc(Auth::user()->email)
            ->send($email);
        }

        return redirect()->route('reunion.index')->with(
            'toast', [
              'icon' => 'success',
              'mensaje' => 'Se creo un acta de reunion correctamente'
          ]);

    }



}
