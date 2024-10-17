<?php

namespace App\Http\Controllers\Email;

use App\Models\Email\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webklex\PHPIMAP\ClientManager;
use App\Http\Controllers\Controller;
use App\Models\Email\UserEmailConfig;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    public function index()
    {

        // Obtén todos los correos electrónicos paginados
        $emails = Email::where('admin_user_id', Auth::user()->id)->with(['status', 'category', 'user'])->paginate(15);

        return view('emails.index', compact('emails'));
    }

    // Mostrar un correo específico
    public function show(Email $email)
    {
        return view('emails.show', compact('email'));
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

}
