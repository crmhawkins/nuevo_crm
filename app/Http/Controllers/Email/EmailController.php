<?php

namespace App\Http\Controllers\Email;

use App\Models\Email\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webklex\PHPIMAP\ClientManager;
use App\Http\Controllers\Controller;
use App\Models\Email\UserEmailConfig;

class EmailController extends Controller
{
    public function index()
    {
        // Obtén todos los correos electrónicos paginados
        $emails = Email::with(['status', 'category', 'user'])->paginate(15);

        return view('emails.index', compact('emails'));
    }

    // Mostrar un correo específico
    public function show(Email $email)
    {
        return view('emails.show', compact('email'));
    }

    public function email(){
        $config = UserEmailConfig::all();
        foreach ($config as $correo) {

            $ClientManager = new ClientManager();
            $client = $ClientManager->make([
                'host' => $correo->host,
                'port' => $correo->port,
                'username' => $correo->username,
                'password' => $correo->password,
                'encryption' => 'ssl',
                'validate_cert' => true,
                'protocol'      => 'imap'
            ]);

            $client->connect();

            $inbox = $client->getFolder('INBOX');
            // Obtener todos los correos
            $messages = $inbox->messages()->unseen()->get();  // También puedes probar con recent()

            // Procesar solo los primeros 10 correos
            $counter = 0;
            foreach ($messages as $message) {
                if ($counter >= 40) break; // Salir del loop después de procesar 10 mensajes

                // Aquí puedes procesar cada correo
                $sender = $message->getFrom()[0]->mail;
                $subject = $message->getSubject();
                $body = $message->getTextBody();
                $messageId = $message->getMessageId(); // Obtiene el Message-ID del correo original

                // Guardar en la base de datos o hacer algo con los correos
                Email::create([
                    'admin_user_id' => $correo->admin_user_id,
                    'sender' => $sender,
                    'subject' => $subject,
                    'body' => $body,
                    'message_id' => $messageId,
                ]);


                // Marca el correo como leído
                $message->setFlag('Seen');

            }

            $client->disconnect();
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

}
