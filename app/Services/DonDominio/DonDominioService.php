<?php

namespace App\Services\DonDominio;

class DonDominioService
{
    protected $apiKey;
    protected $apiSecret;
    protected $baseUrl = 'https://simple-api.dondominio.net/service/api/';

    public function __construct()
    {
        $this->apiKey = config('services.dondominio.api_key');
        $this->apiSecret = config('services.dondominio.api_secret');
    }

    public function domain_check($domain)
    {
        $params = [
            'apiuser' => $this->apiKey,
            'apipasswd' => $this->apiSecret,
            'domain' => $domain
        ];

        $response = $this->makeRequest('domain/check', $params);
        return $response;
    }

    protected function makeRequest($endpoint, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
