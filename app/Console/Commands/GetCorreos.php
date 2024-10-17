<?php

namespace App\Console\Commands;

use App\Models\Email\Email;
use App\Models\Email\UserEmailConfig;
use App\Models\Email\Attachment; // Asegúrate de tener un modelo Attachment
use Illuminate\Console\Command;
use Carbon\Carbon;
use Webklex\PHPIMAP\ClientManager;
use Illuminate\Support\Facades\Storage;

class GetCorreos extends Command
{
    protected $signature = 'correos:get';
    protected $description = 'Obtiene correos y adjuntos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $config = UserEmailConfig::all();

        if ($config->isEmpty()) {
            $this->info('No hay configuraciones de correo disponibles. Abortando comando.');
            return; // Termina la ejecución tempranamente si no hay configuraciones
        }

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
            $messages = $inbox->messages()->unseen()->get();

            foreach ($messages as $message) {
                $messageId = $message->getMessageId();
                if (!Email::where('message_id', $messageId)->exists()) {
                    $sender = $message->getFrom()[0]->mail;
                    $subject = $message->getSubject();
                    $body = $message->getTextBody();

                    // Crear el registro de Email antes de procesar adjuntos
                    $email = Email::create([
                        'admin_user_id' => $correo->admin_user_id,
                        'sender' => $sender,
                        'subject' => $subject,
                        'body' => $body,
                        'message_id' => $messageId,
                    ]);

                    // Procesar adjuntos
                    $attachments = $message->getAttachments();
                    foreach ($attachments as $attachment) {
                        $filename = $attachment->getName(); // Nombre del archivo
                        $file_path = "emails/" . $email->id . "/" . $filename; // Definir ruta de almacenamiento
                        Storage::disk('local')->put($file_path, $attachment->getContent()); // Guardar en el disco local

                        // Guardar registro de adjunto en la base de datos
                        Attachment::create([
                            'email_id' => $email->id,
                            'file_path' => $file_path,
                            'file_name' => $filename,
                        ]);
                    }

                    $message->setFlag('Seen');
                }
            }

            $client->disconnect();
        }
        $this->info('Comando completado: Correos y adjuntos procesados.');
    }
}
