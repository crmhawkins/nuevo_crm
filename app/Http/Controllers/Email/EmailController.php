<?php

namespace App\Http\Controllers\Email;

use App\Models\Email\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webklex\PHPIMAP\ClientManager;
use App\Http\Controllers\Controller;
use App\Models\Email\Attachment;
use App\Models\Email\CategoryEmail;
use App\Models\Email\UserEmailConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmailController extends Controller
{
    public function index()
    {

        // Obtén todos los correos electrónicos paginados
        $emails = Email::where('admin_user_id', Auth::user()->id)
        ->with(['status', 'category', 'user'])
        ->orderBy('created_at', 'desc') // Ordenar por fecha en orden descendente (de más reciente a más antiguo)
        ->paginate(15);
        $categorias = CategoryEmail::all();

        return view('emails.index', compact('emails','categorias'));
    }
    public function create()
    {

        return view('emails.create');
    }

    public function reply($emailId)
    {
        // Busca el email en la base de datos
        $email = Email::find($emailId);

        if (!$email) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Email no encontrado'
            ]);
        }

        // Obtén la configuración de correo electrónico del usuario correspondiente
        $correoConfig = UserEmailConfig::where('admin_user_id', $email->admin_user_id)->first();

        if (!$correoConfig) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Configuración de correo no encontrada para este usuario'
            ]);
        }

        return view('emails.reply', compact('email', 'correoConfig'));
    }

    // Mostrar un correo específico
    public function show(Email $email)
    {

        if ($email->admin_user_id == Auth::user()->id || Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2){
            if(Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2){
                $correo = UserEmailConfig::where('admin_user_id', $email->admin_user_id)->first()->username;
                return view('emails.show', compact('email','correo'));
            }else{
                $correo = UserEmailConfig::where('admin_user_id', $email->admin_user_id)->first()->username;
                if($email->status_id == 1){
                    $email->status_id = 2;
                    $email->save();
                }
                return view('emails.show', compact('email','correo'));
            }

        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No tienes permiso para acceder']);
        }
    }


    public function replyToEmail(Request $request, $emailId)
    {
        // Validar la solicitud
        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'file'
        ]);

        // Busca el email en la base de datos
        $email = Email::find($emailId);

        if (!$email) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Email no encontrado'
            ]);
        }

        // Obtén la configuración de correo electrónico del usuario correspondiente
        $correoConfig = UserEmailConfig::where('admin_user_id', $email->admin_user_id)->first();
        if (!$correoConfig) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Configuración de correo no encontrada para este usuario'
            ]);
        }

        // Conectar a la cuenta de correo del usuario para responder
        $ClientManager = new ClientManager();
        $client = $ClientManager->make([
            'host' => $correoConfig->host,
            'port' => $correoConfig->port,
            'username' => $correoConfig->username,
            'password' => $correoConfig->password,
            'encryption' => 'ssl',
            'validate_cert' => true,
            'protocol' => 'imap'
        ]);

        $client->connect();

        // Preparar los encabezados para la respuesta
        $messageId = $email->message_id;
        $recipient = $email->sender;  // Aquí obtenemos el destinatario original

        // Configurar la respuesta con adjuntos
        Mail::send([], [], function ($message) use ($request, $recipient, $email, $messageId, $correoConfig) {
            $firma = $correoConfig->firma;
            $mensajeConFirma = $request->message . "<br><br>" . $firma;
            $message->from($correoConfig->username)
                    ->to($recipient)
                    ->subject('Re: ' . $email->subject)
                    ->html($mensajeConFirma)
                    ->replyTo($correoConfig->username)
                    ->getHeaders()
                    ->addTextHeader('In-Reply-To', $messageId)
                    ->addTextHeader('References', $messageId);

            // Adjuntar archivos si existen
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $message->attach($attachment->getRealPath(), [
                        'as' => $attachment->getClientOriginalName(),
                        'mime' => $attachment->getClientMimeType(),
                    ]);
                }
            }
        });

        // Guardar el correo como respuesta en la base de datos
        $responseEmail = Email::create([
            'admin_user_id' => $correoConfig->admin_user_id,
            'sender' => $correoConfig->username,
            'to' => $recipient,
            'subject' => 'Re: ' . $email->subject,
            'body' => $request->message,
            'message_id' => uniqid(),
            'category_id' => 6,
        ]);

        // Guardar los archivos adjuntos en el sistema de almacenamiento y en la base de datos
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $filename = $attachment->getClientOriginalName();
                $file_path = "emails/" . $responseEmail->id . "/" . $filename;
                Storage::disk('public')->put($file_path, file_get_contents($attachment->getRealPath()));

                Attachment::create([
                    'email_id' => $responseEmail->id,
                    'file_path' => $file_path,
                    'file_name' => $filename,
                ]);
            }
        }

        $client->disconnect();

        return response()->json(['status' => 'Respuesta enviada correctamente con adjuntos']);
    }

    public function sendEmail(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
            'attachments.*' => 'file'
        ]);

        // Obtén la configuración de correo electrónico del usuario correspondiente
        $correoConfig = UserEmailConfig::where('admin_user_id', auth()->id())->first();

        if (!$correoConfig) {
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'Configuración de correo no encontrada para este usuario'
            ]);
        }

        // Configurar y enviar el nuevo mensaje con adjuntos
        Mail::send([], [], function ($message) use ($request, $correoConfig) {
            $firma = $correoConfig->firma;
            $mensajeConFirma = $request->message . "<br><br>" . $firma;
            $message->from($correoConfig->username)
                    ->to($request->to)
                    ->subject($request->subject)
                    ->html($mensajeConFirma)
                    ->replyTo($correoConfig->username);

            // Adjuntar archivos si existen
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $message->attach($attachment->getRealPath(), [
                        'as' => $attachment->getClientOriginalName(),
                        'mime' => $attachment->getClientMimeType(),
                    ]);
                }
            }
        });

         // Guardar el correo como enviado en la base de datos
        $email = Email::create([
            'admin_user_id' => $correoConfig->admin_user_id,
            'sender' => $correoConfig->username,
            'to' => $request->to,
            'subject' => $request->subject,
            'body' => $request->message,
            'message_id' => uniqid(),
            'category_id' => 6,
        ]);

        // Guardar los archivos adjuntos en el sistema de almacenamiento y en la base de datos
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $filename = $attachment->getClientOriginalName();
                $file_path = "emails/" . $email->id . "/" . $filename;
                Storage::disk('public')->put($file_path, file_get_contents($attachment->getRealPath()));

                Attachment::create([
                    'email_id' => $email->id,
                    'file_path' => $file_path,
                    'file_name' => $filename,
                ]);
            }
        }

        return redirect()->route('admin.emails.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Correo enviado correctamente con adjuntos'
        ]);
    }

    public function countUnread() {
        $count = Email::where('admin_user_id', Auth::user()->id)->where('status', 1)->count();
        return response()->json([
            'count' => $count
        ]);
    }

}
