<?php

namespace App\Console\Commands;

use App\Models\Email\Email;
use App\Models\Email\UserEmailConfig;
use App\Models\Email\Attachment;
use Illuminate\Console\Command;
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
            return;
        }

        foreach ($config as $correo) {
            try {
                $ClientManager = new ClientManager();
                $client = $ClientManager->make([
                    'host' => $correo->host,
                    'port' => $correo->port,
                    'username' => $correo->username,
                    'password' => $correo->password,
                    'encryption' => 'ssl',
                    'validate_cert' => true,
                    'protocol' => 'imap',
                ]);

                $client->connect();

                $inbox = $client->getFolder('INBOX');
                $messages = $inbox->messages()->unseen()->limit(10)->get();

                foreach ($messages as $message) {
                    try {
                        $messageId = $message->getMessageId();

                        $sender = $message->getFrom()[0]->mail;
                        $subject = $message->getSubject();
                        $body = $message->getHTMLBody() ?: $message->getTextBody();

                        $toRecipients = $message->getTo();
                        $ccRecipients = $message->getCc();

                        $toList = collect($toRecipients)->pluck('mail')->implode(', ');
                        $ccList = collect($ccRecipients)->pluck('mail')->implode(', ');

                        $email = Email::create([
                            'admin_user_id' => $correo->admin_user_id,
                            'sender' => $sender,
                            'subject' => $subject,
                            'body' => $body,
                            'message_id' => $messageId,
                            'status_id' => 1,
                            'cc' => $ccList,
                            'to' => $toList,
                        ]);

                        $attachments = $message->getAttachments();
                        foreach ($attachments as $attachment) {
                            try {
                                $filename = $attachment->getName();
                                $file_path = "emails/" . $email->id . "/" . $filename;
                                Storage::disk('public')->put($file_path, $attachment->getContent());

                                $cid = $attachment->getContentId();
                                if ($cid) {
                                    $cid = str_replace(['<', '>'], '', $cid);
                                    $public_path = asset('storage/' . $file_path);
                                    $body = str_replace("cid:$cid", $public_path, $body);
                                }

                                Attachment::create([
                                    'email_id' => $email->id,
                                    'file_path' => $file_path,
                                    'file_name' => $filename,
                                ]);
                            } catch (\Exception $e) {
                                $this->error("Error procesando adjunto: {$e->getMessage()}");
                            }
                        }

                        $email->update(['body' => $body]);
                        $message->setFlag('Seen');
                        if($correo->admin_user_id != 54){
                            $message->delete(); // Elimina el mensaje del servidor
                        }

                    } catch (\Exception $e) {
                        $this->error("Error procesando mensaje: {$e->getMessage()}");
                    }
                }
                    $this->info("Correos procesados para '{$correo->username}'");
                $client->disconnect();
            } catch (\Exception $e) {
                $this->error("Error con la configuración del correo '{$correo->username}': {$e->getMessage()}");
            }
        }

        $this->info('Comando completado: Correos y adjuntos procesados.');
    }
}
