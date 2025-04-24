<?php

namespace App\Http\Controllers\AutomatizacionKit;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;

class ReadEmailKitController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $client = Client::account('auditoriashawkins');
            $client->connect();

            $folder = $client->getFolder('INBOX');

            // Modificación: Usar whereFrom en lugar de filtrar después
            $messages = $folder->query()
                ->from('infodigitalizador@acelerapyme.gob.es')
                ->limit(10)
                ->get()
                ->sortByDesc(fn($m) => $m->getDate());

            $bodies = [];

            foreach ($messages as $msg) {
                $rawBody = $msg->hasHTMLBody() ? $msg->getHTMLBody() : $msg->getTextBody();
    
                $main = preg_split('/^-{2,}Mensaje original-{2,}/mi', $rawBody, 2)[0];
                $main = preg_split('/<blockquote/i', $main, 2)[0];
                
                $main = preg_replace('/<br[^>]*>/i', "\n", $main);
                $main = preg_replace('/<\/p>/i', "\n\n", $main);
                $plain = trim(strip_tags($main));
            
                $plain = preg_replace('/AVISO:.*$/s', '', $plain);   // ajusta al patrón real
            
                $bodies[] = $plain;
            }

            return response()->json(['correos' => $bodies], 200);

        } catch (ConnectionFailedException|RuntimeException|\Throwable $e) {
            Log::error('ReadEmailKitController error: '.$e->getMessage());
            return response()->json([
                'error' => 'No se pudieron leer los correos: '.$e->getMessage()
            ], 500);
        }
    }
}