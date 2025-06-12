<?php

namespace App\Http\Controllers\DonDominio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dondominio\API\API;

class DonDominioController extends Controller
{
    protected $dondominio;

    public function __construct()
    {
        $this->dondominio = new API([
            'apiuser'   => env('DON_DOMINIO_USERNAME'),
            'apipasswd' => env('DON_DOMINIO_PASSWORD')
        ]);
    }

    public function checkDomain($domain)
    {
        $response = $this->dondominio->call('domain/check', ['domain' => $domain]);

        if (is_string($response)) {
            $response = json_decode($response, true);
        }


        if (isset($response['success']) && $response['success'] && isset($response['responseData']['domains'][0])) {
            $domainData = $response['responseData']['domains'][0];
            $available = $domainData['available'];
            $price = $domainData['price'];

            $price = $price * 1.21;

            if ($available == 'true')
            {
                return response()->json([
                    'available' => $available,
                    'price'     => $price
                ], 200);
            } else {
                return response()->json([
                    'message' => "Dominio no disponible",
                ], 409);
            }

        } else {
            $errorMessage = 'No se pudo verificar el dominio';

            if (isset($response['errorCodeMsg']) && !empty($response['errorCodeMsg'])) {
                $errorMessage = $response['errorCodeMsg'];
            }

            return response()->json([
                'error' => $errorMessage
            ]);
        }
    }

    public function getBalance()
    {
        $response = $this->dondominio->call('account/info/', []);

        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        $balance = $response['responseData']['balance'];
        return response()->json($balance);
    }

    public function createSubdomain(Request $request)
    {
        $domain = $request->domain;
        $subdomain = $request->subdomain;

        $response = $this->dondominio->call('service/subdomaincreate/', [
            'serviceName' => $domain,
            'name' => $subdomain.'.'.$domain,
            'ftpPath' => 'public-test/'
        ]);

        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        $success = $response['success'];
        if ($success == "true") {
            return response()->json(
                [
                "message" => "Subdominio creado con éxito"
            ], 200);
        } else {
            return response()->json([
                "message" => "Error al crear el subdominio",
                "response" => $response,
            ], 409);
        }
    }

    public function updateDnsRecords(Request $request)
    {
        $domain = $request->domain;
        $full_domain = $request->full_domain;
        $record = $request->record;
        $ip = $request->ip;

        $dnsZoneData = [
            [
                "name" => $full_domain,
                "type" => $record,
                "ttl" => 600,
                "value" => $ip
            ],
            [
                "name" => "www.".$full_domain,
                "type" => $record,
                "ttl" => 600,
                "value" => $ip
            ],
        ];

        $dnsZoneData = base64_encode(json_encode($dnsZoneData));

        $response = $this->dondominio->call('service/dnssetzone', [
            'serviceName' => $domain,
            'dnsZoneData' => $dnsZoneData,
        ]);

        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        $success = $response['success'];
        if ($success == "true") {
            return response()->json([
                "message" => "Registro ".$record." para el dominio ".$domain." cambiado con éxito"
            ], 200);
        } else {
            return response()->json([
                "message" => "Error al cambiar el registro ".$record." para el dominio ".$domain,
                "response" => $response,
            ], 400);
        }
    }

    public function changeDnsRecords(Request $request)
    {
        $subdomain = $request->subdomain;
        $record = $request->record;
        $ip = $request->ip;

        $full_domain = $subdomain.".herasoft.es";

        $response = $this->dondominio->call('service/dnscreate', [
            'serviceName' => "herasoft.es",
            "name" => $full_domain,
            "type" => $record,
            "ttl" => 600,
            "value" => $ip
        ]);


        $response_www = $this->dondominio->call('service/dnscreate', [
            'serviceName' => "herasoft.es",
            "name" => "www.".$full_domain,
            "type" => $record,
            "ttl" => 600,
            "value" => $ip
        ]);


        if (is_string($response) && is_string($response_www)) {
            $response = json_decode($response, true);
            $response_www = json_decode($response_www, true);
        }

        $success = $response['success'];
        $success_www = $response_www['success'];

        if ($success == "true" && $success_www == "true") {
            return response()->json([
                "message" => "Registro ".$record." para el dominio ".$full_domain." cambiado con éxito"
            ], 200);
        } else {
            return response()->json([
                "message" => "Error al cambiar el registro ".$record." para el dominio ".$full_domain,
                "response" => $response,
            ], 400);
        }
    }

    public function registerDomain(Request $request)
    {
        $domain = $request->domain;

        $ownerContact = [

        ];

        $ownerContact = json_encode($ownerContact);

        $response = $this->dondominio->call('domain/create', [
            'domain' => $domain,
            'period' => 1,
            "ownerContactType" => "individual",
            "ownerContactFirstName" => "Elena",
            "ownerContactLastName" => "Perez",
            "ownerContactIdentNumber" => "75900659S",
            "ownerContactEmail" => "administracion@hawkins.es",
            "ownerContactPhone" => "+34.622440984",
            "ownerContactAddress" => "C/General primo de rivera S/N",
            "ownerContactPostalCode" => "11201",
            "ownerContactCity" => "Algeciras",
            "ownerContactState" => "Andalucia",
            "ownerContactCountry" => "ES"
        ]);

        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        $success = $response['success'];
        if ($success == "true") {
            return response()->json([
                "message" => "Dominio ".$domain." registrado con éxito"
            ], 200);
        } else {
            return response()->json([
                "message" => "Error al registrar el dominio ".$domain,
                "response" => $response,
            ], 400);
        }
    }
}
