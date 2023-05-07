<?php

namespace App\Modules\Account\Services;

use App\Helpers\UrlList;
use App\Modules\Account\Requests\AccountRequest;
use App\Modules\Token\Services\AccessTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AccountService
{

    public function create(AccountRequest $request): JsonResponse
    {
        $token = substr($request->header("Authorization"), 7);
        $response = $this->sendCreateRequest($request, $token);

        $data = $response->json();

        if (!empty($data["status"]) and $data["status"] === "error") {
            if ($data["code"] === "INVALID_TOKEN") {
                AccessTokenService::refreshWithCallback(
                    $token,
                    $request,
                    function($param) {
                        $this->create($param);
                    }
                );
            }
        }

        return response()->json(["data" => [
            "status" => "success",
        ], 'token' => substr($request->header("Authorization"), 7)], 201);
    }

    private function sendCreateRequest(
        AccountRequest $request,
        string $token
    ): object {
        return Http::withHeaders([
            "Authorization" => "Bearer $token",
            "Content-Type" => "application/json"
        ])
            ->asJson()
            ->post(
                env("ZOHO_API_DOMAIN") . UrlList::CREATE_ACCOUNT,
                json_decode(
                    json_encode(
                        ["data" => [
                            [
                                "Website" => $request->input("website"),
                                "Phone" => $request->input("phone"),
                                "Account_Name" => $request->input("account_name")
                            ]
                        ]
                        ]
                    )
                )

            );
    }

}
