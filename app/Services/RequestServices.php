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

    public static function create(
        string $token,
        array $bodyArray,
        string $url
    )
    {
        $client = new Client();

        $url = env("ZOHO_API_DOMAIN") . $url;

        $headers = [
            "Authorization" => "Bearer $token",
            "Content-Type" => "application/json",
        ];

        $body = json_decode(
            json_encode(
                $bodyArray
            )
        );

        $response = $client->post($url, [
            "headers" => $headers,
            "json" => $body,
            'http_errors' => false
        ]);
        $responseBody = (string) $response->getBody();

        if (isset(json_decode($responseBody)->data)) {
            $response = json_decode($responseBody)->data[0];
        } else {
            $response = json_decode($responseBody);
        }
        $response->token = $token;

        return $response;
    }

}
