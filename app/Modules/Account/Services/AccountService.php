<?php

namespace App\Modules\Account\Services;

use App\Helpers\UrlList;
use App\Modules\Account\Requests\AccountRequest;
use App\Modules\Token\Services\AccessTokenService;
use App\Services\RequestServices;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AccountService
{

    public function create(AccountRequest $request): JsonResponse
    {
        $token = substr($request->header("Authorization"), 7);
        $response = $this->sendCreateRequest($request, $token);
        if (!empty($response->status) and $response->status == "error") {
            if ($response->code == "INVALID_TOKEN") {
                AccessTokenService::refreshWithCallback(
                    $token,
                    $request,
                    function($token, $request) {
                        return $this->sendCreateRequest($request, $token);
                    }
                );
            }
        }

        $newToken = Session::get("token");
        Session::forget("token");

        return response()->json(["data" => [
            "status" => "success",
        ], 'token' => $newToken ?? $response->token
        ], 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $token = substr($request->header("Authorization"), 7);
        $response = RequestServices::getAll($token, UrlList::GET_ACCOUNTS);

        if (!empty($response->status) and $response->status == "error") {
            if ($response->code == "INVALID_TOKEN") {
                $response = AccessTokenService::refreshWithCallback(
                    $token,
                    $request,
                    function($token, $request) {
                        return RequestServices::getAll($token, UrlList::GET_ACCOUNTS);
                    }
                );
            }
        }

        $preparedAccounts = $this->prepareAccounts($response->data);

        return response()->json(
            [
                "data" => [$preparedAccounts],
                "token" => $response->token
            ]
        );
    }

    private function sendCreateRequest(
        AccountRequest $request,
        string $token
    ) {
        $client = new Client();

        $url = env("ZOHO_API_DOMAIN") . UrlList::CREATE_ACCOUNT;

        $headers = [
            "Authorization" => "Bearer $token",
            "Content-Type" => "application/json",
        ];

        $body = json_decode(
                    json_encode(
                        ["data" => [
                            [
                                "Account_Name" => $request->input("account_name"),
                                "Website" => $request->input("website"),
                                "Phone" => $request->input("phone"),
                            ]
                        ]
                        ]
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

    private function prepareAccounts(array $accounts): array
    {
        $accountsList = [];

        foreach ($accounts as $account) {
            $accountsList[] = [
                "id" => $account->id,
                "account_name" => $account->Account_Name
            ];
        }

        return $accountsList;
    }

}
