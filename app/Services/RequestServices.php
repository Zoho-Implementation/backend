<?php

namespace App\Services;

use GuzzleHttp\Client;

class RequestServices
{

    public static function getAll(string $token, string $url)
    {
        $client = new Client();

        $headers = [
            'Authorization' => 'Bearer ' .$token,
            'Content-Type' => 'application/json',
        ];

        $response = $client->request('GET', env("ZOHO_API_DOMAIN") . $url, [
            'headers' => $headers,
            'http_errors' => false
        ]);


        $body = $response->getBody()->getContents();
        $response = json_decode($body);
        $response->token = $token;

        return $response;
    }

}
