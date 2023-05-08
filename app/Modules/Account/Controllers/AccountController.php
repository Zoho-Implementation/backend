<?php

namespace App\Modules\Account\Controllers;

use App\Helpers\Constants;
use App\Helpers\UrlList;
use App\Http\Controllers\Controller;
use App\Modules\Account\Requests\AccountRequest;
use App\Modules\Account\Services\AccountService;
use App\Modules\Token\Services\AccessTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AccountController extends Controller
{

    public function getAll(Request $request): JsonResponse
    {
        $token = substr($request->header("Authorization"), 7);
        $response =  Http::withHeaders([
            "Authorization" => "Bearer $token",
            "Content-Type" => "application/json"
        ])
            ->asJson()
            ->get(env("ZOHO_API_DOMAIN") . UrlList::GET_ACCOUNTS);
        $accounts = $response->json();

        if (!empty($accounts["status"]) and $accounts["status"] === "error") {
            if ($accounts["code"] === Constants::INVALID_TOKEN) {
                AccessTokenService::refreshWithCallback(
                    $token,
                    $request,
                    function($param) {
                        $this->getAll($param);
                    }
                );
            }
        }

        $accountsList = [];
        foreach ($accounts['data'] as $account) {
            $accountsList[] = [
                "id" => $account["id"],
                "account_name" => $account["Account_Name"]
            ];
        }
        return response()->json(["data" => [$accountsList], "token" => $token]);
    }

    public function create(AccountRequest $request): JsonResponse
    {
        $accountService = new AccountService();
        return $accountService->create($request);
    }

}
