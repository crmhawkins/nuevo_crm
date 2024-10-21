<?php

namespace App\Http\Controllers\Email;

use App\Models\Email\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webklex\PHPIMAP\ClientManager;
use App\Http\Controllers\Controller;
use App\Models\Email\Attachment;
use App\Models\Email\UserEmailConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Part\TextPart;

class EmailController extends Controller
{
    public function index()
    {

        // Obtén todos los correos electrónicos paginados
        $emails = Email::where('admin_user_id', Auth::user()->id)->with(['status', 'category', 'user'])->paginate(15);

        return view('emails.index', compact('emails'));
    }
    public function create()
    {

        return view('emails.create');
    }

    // Mostrar un correo específico
    public function show(Email $email)
    {

        if ($email->admin_user_id == Auth::user()->id || Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2){
            if(Auth::user()->access_level_id == 1 || Auth::user()->access_level_id == 2){
                return view('emails.show', compact('email'));
            }else{
                $email->status_id = 2;
                $email->save();
                return view('emails.show', compact('email'));
            }

        }else{
            return redirect()->back()->with('toast', [
                'icon' => 'error',
                'mensaje' => 'No tienes permiso para acceder']);
        }
    }


    public function replyToEmail($emailId)
    {
        // Busca el email en la base de datos
        $email = Email::find($emailId);

        if (!$email) {
            return response()->json(['error' => 'Email no encontrado'], 404);
        }

        // Obtén la configuración de correo electrónico del usuario correspondiente
        $correoConfig = UserEmailConfig::where('admin_user_id', $email->admin_user_id)->first();

        if (!$correoConfig) {
            return response()->json(['error' => 'Configuración de correo no encontrada para este usuario'], 404);
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

        // Cargar el correo original desde la carpeta
        $inbox = $client->getFolder('INBOX');
        $originalMessage = $inbox->messages()->whereMessageId($email->message_id)->get()->first();

        if (!$originalMessage) {
            return response()->json(['error' => 'Correo original no encontrado'], 404);
        }

        // Preparar los encabezados para la respuesta
        $messageId = $originalMessage->getMessageId();
        $recipient = $email->sender;  // Aquí obtenemos el destinatario original

        // Configurar la respuesta
        Mail::send([], [], function ($message) use ($recipient, $email, $messageId, $correoConfig) {
            $message->from($correoConfig->username)
                    ->to($recipient)
                    ->subject('Re: ' . $email->subject)
                    ->setBody('Esta es una respuesta al correo original.', 'text/html')
                    ->setReplyTo($correoConfig->username)
                    ->getHeaders()
                    ->addTextHeader('In-Reply-To', $messageId)
                    ->addTextHeader('References', $messageId);
        });

        $client->disconnect();

        return response()->json(['status' => 'Respuesta enviada correctamente']);
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
            return response()->json(['error' => 'Configuración de correo no encontrada para este usuario'], 404);
        }

        // Configurar y enviar el nuevo mensaje con adjuntos
        Mail::send([], [], function ($message) use ($request, $correoConfig) {
            $message->from($correoConfig->username)
                    ->to($request->to)
                    ->subject($request->subject)
                    ->html(new TextPart($request->message, 'utf-8', 'html'))
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

        return response()->redirect('admin.emails.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Correo enviado correctamente con adjuntos'
        ]);
    }


}
