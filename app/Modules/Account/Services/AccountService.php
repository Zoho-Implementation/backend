<?php

namespace App\Modules\Account\Services;

use App\Helpers\UrlList;
use App\Modules\Account\Requests\AccountRequest;
use App\Modules\Token\Services\AccessTokenService;
use App\Services\RequestServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AccountService
{

    public function create(AccountRequest $request): JsonResponse
    {
        $token = substr($request->header("Authorization"), 7);
        $preparedBody = ["data" => [
            [
                "Account_Name" => $request->input("account_name"),
                "Website" => $request->input("website"),
                "Phone" => $request->input("phone"),
            ]
        ]
        ];

        $response = RequestServices::create(
            $token,
            $preparedBody,
            UrlList::CREATE_ACCOUNT
        );

        if (!empty($response->status) and $response->status == "error") {
            if ($response->code == "INVALID_TOKEN") {
                AccessTokenService::refreshWithCallback(
                    $token,
                    $request,
                    function($token, $request) use ($preparedBody) {
                        return RequestServices::create(
                            $token,
                            $preparedBody,
                            UrlList::CREATE_ACCOUNT
                        );
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
