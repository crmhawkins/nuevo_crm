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
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Symfony\Component\Mime\Part\TextPart;


class EmailController extends Controller
{
    public function index(Request $request)
    {
        $categoriaId = $request->input('categoria_id', null);  // Por defecto no filtra por categoría

        // Filtra los correos según la categoría si se especifica una
        $query = Email::where('admin_user_id', Auth::user()->id)
                      ->with(['status', 'category', 'user'])
                      ->orderBy('created_at', 'desc');

        if ($categoriaId) {
            $query->where('category_id', $categoriaId);
        }

        $emails = $query->paginate(15);  // Pagina los resultados

        $categorias = CategoryEmail::all();

        return view('emails.index', compact('emails', 'categorias', 'categoriaId'));
    }

    public function create()
    {

        $Emails = Email::select('to')
                            ->distinct()
                            ->whereNotNull('to')
                            ->pluck('to');


                            $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

        // Arreglo para almacenar correos separados
        $separatedEmails = [];

        foreach ($Emails as $email) {
            // Aplicamos la expresión regular para extraer todos los correos electrónicos en cada elemento
            preg_match_all($emailPattern, $email, $matches);

            // Añadimos los correos encontrados al array de correos separados
            $separatedEmails = array_merge($separatedEmails, $matches[0]);
        }

        // Filtramos los correos para eliminar los que contienen 'guest.booking.com'
        $previousEmails = array_filter($separatedEmails, function ($email) {
            return !str_contains($email, '@guest.booking.com');
        });

        return view('emails.create',compact('previousEmails'));
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

    public function forward($emailId)
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

        return view('emails.forward', compact('email', 'correoConfig'));
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

    public function forwardEmail(Request $request, $emailId)
    {
        // Validar la solicitud
        $request->validate([
            'message' => 'required|string',
            'to' => 'required|email',  // Añadimos el campo 'to' para definir el destinatario al reenviar
            'cc' => 'nullable|string',
            'bcc' => 'nullable|string',
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

        // Configurar el envío con los datos del usuario
        config([
            'mail.mailers.smtp.host' => $correoConfig->smtp_host,
            'mail.mailers.smtp.port' => $correoConfig->smtp_port,
            'mail.mailers.smtp.username' => $correoConfig->username,
            'mail.mailers.smtp.password' => $correoConfig->password,
            'mail.mailers.smtp.encryption' => 'ssl',
            'mail.from.address' => $correoConfig->username, // El correo del remitente
            'mail.from.name' => $correoConfig->user->name . ' ' . $correoConfig->user->surname, // Nombre del remitente
        ]);

        // Configurar el reenvío con adjuntos
        Mail::send([], [], function ($message) use ($request, $email, $correoConfig) {
            $firma = $correoConfig->firma;
            $mensajeConFirma = $request->message . "<br><br>" . $firma;

            $message->from($correoConfig->username)
                    ->to($request->to)  // Aquí el destinatario del reenvío
                    ->subject('Fwd: ' . $email->subject)
                    ->html($mensajeConFirma)
                    ->replyTo($correoConfig->username);

                // Añadir CC y BCC si están presentes
                if ($request->filled('cc')) {
                    $message->cc(explode(',', $request->cc));
                }

                if ($request->filled('bcc')) {
                    $message->bcc(explode(',', $request->bcc));
                }

            // Adjuntar archivos originales si existen
            foreach ($email->attachments as $attachment) {
                $message->attach(storage_path('app/public/' . $attachment->file_path), [
                    'as' => $attachment->file_name,
                    'mime' => mime_content_type(storage_path('app/public/' . $attachment->file_path)),
                ]);
            }

            // Adjuntar archivos nuevos si existen
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $newAttachment) {
                    $message->attach($newAttachment->getRealPath(), [
                        'as' => $newAttachment->getClientOriginalName(),
                        'mime' => $newAttachment->getClientMimeType(),
                    ]);
                }
            }
        });

        $firma = $correoConfig->firma;
        $mensajeConFirma = $request->message . "<br><br>" . $firma;

        // Guardar el correo reenviado como respuesta en la base de datos
        $forwardedEmail = Email::create([
            'admin_user_id' => $correoConfig->admin_user_id,
            'sender' => $correoConfig->username,
            'to' => $request->to,
            'cc' => $request->cc,
            'subject' => 'Fwd: ' . $email->subject,
            'body' => $mensajeConFirma,
            'message_id' => uniqid(),
            'category_id' => 6,
        ]);

        // Guardar los archivos adjuntos en el sistema de almacenamiento y en la base de datos
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $newAttachment) {
                $filename = $newAttachment->getClientOriginalName();
                $file_path = "emails/" . $forwardedEmail->id . "/" . $filename;
                Storage::disk('public')->put($file_path, file_get_contents($newAttachment->getRealPath()));

                Attachment::create([
                    'email_id' => $forwardedEmail->id,
                    'file_path' => $file_path,
                    'file_name' => $filename,
                ]);
            }
        }

        return redirect()->route('admin.emails.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Correo reenviado correctamente'
        ]);
    }


    public function replyToEmail(Request $request, $emailId)
    {
        // Validar la solicitud
        $request->validate([
            'message' => 'required|string',
            'cc' => 'nullable|string',
            'bcc' => 'nullable|string',
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

        config([
            'mail.mailers.smtp.host' => $correoConfig->smtp_host,
            'mail.mailers.smtp.port' => $correoConfig->smtp_port,
            'mail.mailers.smtp.username' => $correoConfig->username,
            'mail.mailers.smtp.password' => $correoConfig->password,
            'mail.mailers.smtp.encryption' => 'ssl',
            'mail.from.address' => $correoConfig->username, // El correo del remitente
            'mail.from.name' => $correoConfig->user->name.' '.$correoConfig->user->surname, // Nombre del remitente
        ]);

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

                    if ($request->filled('cc')) {
                        $message->cc(explode(',', $request->cc));
                    }

                    if ($request->filled('bcc')) {
                        $message->bcc(explode(',', $request->bcc));
                    }

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

        $firma = $correoConfig->firma;
        $mensajeConFirma = $request->message . "<br><br>" . $firma;

        // Guardar el correo como respuesta en la base de datos
        $responseEmail = Email::create([
            'admin_user_id' => $correoConfig->admin_user_id,
            'sender' => $correoConfig->username,
            'to' => $recipient,
            'cc' => $request->cc,
            'subject' => 'Re: ' . $email->subject,
            'body' => $mensajeConFirma,
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

        return redirect()->route('admin.emails.index')->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Correo enviado correctamente '
        ]);
    }

    public function sendEmail(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
            'cc' => 'nullable|string',
            'bcc' => 'nullable|string',
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
        config([
            'mail.mailers.smtp.host' => $correoConfig->smtp_host,
            'mail.mailers.smtp.port' => $correoConfig->smtp_port,
            'mail.mailers.smtp.username' => $correoConfig->username,
            'mail.mailers.smtp.password' => $correoConfig->password,
            'mail.mailers.smtp.encryption' => 'ssl',
            'mail.from.address' => $correoConfig->username, // El correo del remitente
            'mail.from.name' => $correoConfig->user->name.' '.$correoConfig->user->surname, // Nombre del remitente
        ]);
        // Configurar y enviar el nuevo mensaje con adjuntos
        Mail::send([], [], function ($message) use ($request, $correoConfig) {
            $firma = $correoConfig->firma;

            $mensajeConFirma = $request->message . "<br><br>" . $firma;
            $mensajeTextoPlano = strip_tags($request->message . "\n\n" . $firma); // Versión en texto plano


            $message->from($correoConfig->username)
                    ->to($request->to)
                    ->subject($request->subject)
                    ->html($mensajeConFirma)
                    ->text($mensajeTextoPlano)  // Agregar texto plano
                    ->replyTo($correoConfig->username);

                    if ($request->filled('cc')) {
                        $message->cc(explode(',', $request->cc));
                    }

                    if ($request->filled('bcc')) {
                        $message->bcc(explode(',', $request->bcc));
                    }

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

        $firma = $correoConfig->firma;
        $mensajeConFirma = $request->message . "<br><br>" . $firma;

         // Guardar el correo como enviado en la base de datos
        $email = Email::create([
            'admin_user_id' => $correoConfig->admin_user_id,
            'sender' => $correoConfig->username,
            'to' => $request->to,
            'cc' => $request->cc,
            'subject' => $request->subject,
            'body' => $mensajeConFirma,
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
            'mensaje' => 'Correo enviado correctamente '
        ]);
    }

    public function countUnread() {
        $count = Email::where('admin_user_id', Auth::user()->id)->where('status_id', 1)->count();
        return response($count);
    }

    public function destroy(Request $request) {
        $email = Email::find($request->id);
        if (!$email) {
            return response()->json([
                'status' => false,
                'mensaje' => 'Correo no encontrado'
            ]);
        }
        if ($email->admin_user_id == Auth::user()->id) {
            $email->delete();
            return response()->json([
                'status' => true,
                'mensaje' => 'Correo eliminado correctamente'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => 'No tienes permisos para eliminar este correo'
            ]);
        }
    }

}
