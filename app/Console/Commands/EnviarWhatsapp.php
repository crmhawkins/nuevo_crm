<?php

namespace App\Console\Commands;

use App\Imports\ExcelSimpleImport;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\UnclassifiedExpenses;
use App\Models\Accounting\UnclassifiedIncome;
use App\Models\Alerts\Alert;
use App\Models\Budgets\Budget;
use App\Models\Clients\Client;
use App\Models\Invoices\Invoice;
use App\Models\Plataforma\CampaniasWhatsapp;
use App\Models\Plataforma\MensajesPendientes;
use App\Models\Plataforma\ModeloMensajes;
use App\Models\Users\User;
use Http;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class EnviarWhatsapp extends Command
{
    protected $signature = 'Whatsapp:Enviar';
    protected $description = 'Envia mensajes pendientes de whatsapp';

    public function __construct()
    {
        parent::__construct();
    }

    private function replaceTemplateVariables($message, $client = null)
    {
        $variables = [
            '{cliente}' => $client ? $client->name : '',
            '{fecha}' => now()->format('d/m/Y'),
            '{telefono}' => $client ? $client->phone : '',
            '{email}' => $client ? $client->email : '',
            '{direccion}' => $client ? $client->address : '',
        ];

        return str_replace(array_keys($variables), array_values($variables), $message);
    }

    public function handle()
    {
        $campania = CampaniasWhatsapp::where('estado', 0)->first();
        $pendientes = MensajesPendientes::where('status', 0)->first();
        if ($campania && $pendientes) {
        $mensaje = $pendientes->message;
        $clientId = $pendientes->client_id;
        $cliente = Client::find($clientId);
        $phone = $cliente->phone;
        $phone = str_replace([' ', '+'], '', $phone);
        if (str_starts_with($phone, '34')) {
            $phone = substr($phone, 2);
        }
        $phone = '34' . $phone;
        $mensaje = $this->replaceTemplateVariables($mensaje, $cliente);
        $this->info('Enviando mensaje a ' . $cliente->name);
        $this->info('Mensaje: ' . $mensaje);
        $this->info('--------------------------------');
        Http::post('http://127.0.0.1:8080/send-message', [
            'chatId' => $phone,
            'message' => $mensaje,
        ]);
        $this->info('Mensaje enviado correctamente');
            $pendientes->status = 1;
            $pendientes->save();
        }
    }

    private function excelDateToDate($excelSerial)
    {
        $unixTimestamp = ($excelSerial - 25569) * 86400; // 25569 = días entre 1/1/1900 y Unix Epoch
        return gmdate('Y-m-d', $unixTimestamp);
    }
}
