<?php

namespace App\Http\Controllers;

use App\Models\Plataforma\WhatsappConfig;
use Illuminate\Http\Request;

class PlataformaWhatsappApi extends Controller
{
    public function getTemplateStatus() {
        $businessId = env('BUSINESS_ID', '113437731696576');
        $url = 'https://graph.facebook.com/v22.0/' . $businessId . '/message_templates?fields=name,status';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . env('WHATSAPP_TOKEN'),
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return response()->json(json_decode($response));
        }


        dd($response);
        return response()->json(['error' => 'Failed to get template status'], $httpCode);
    }

    public function connectWhatsapp() {

    }

    public function checkApiKey($apikey) {
        $apikey = WhatsappConfig::where('apikey', $apikey)->first();
        if ($apikey) {
            return true;
        }
        return false;
    }
}
