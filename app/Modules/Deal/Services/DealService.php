<?php

namespace App\Modules\Deal\Services;

use App\Helpers\UrlList;
use App\Modules\Deal\Requests\DealRequest;
use App\Modules\Token\Services\AccessTokenService;
use App\Services\RequestServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DealService
{

    public function getAll(Request $request): JsonResponse
    {
        $token = substr($request->header("Authorization"), 7);

        $response = RequestServices::getAll($token, UrlList::GET_DEALS);

        if (!empty($response->status) and $response->status == "error") {
            if ($response->code == "INVALID_TOKEN") {
                $response = AccessTokenService::refreshWithCallback(
                    $token,
                    $request,
                    function($token, $request) {
                        return RequestServices::getAll($token, UrlList::GET_DEALS);
                    }
                );
            }
        }

        $preparedResponse = $this->prepareDeals($response->data);

        return response()->json([
            "data" => [$preparedResponse],
            "token" => $response->token
        ]);
    }

    public function create(DealRequest $request): JsonResponse
    {
        $token = substr($request->header("Authorization"), 7);

        $preparedBody = ["data" => [
            [
                "Account_Name" => [
                    "id" => $request->input("account_id")
                ],
                "Deal_Name" => $request->input("deal_name"),
                "Stage" => $request->input("stage"),
            ]
        ]
        ];

        $response = RequestServices::create(
            $token,
            $preparedBody,
            UrlList::CREATE_DEAL
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
                            UrlList::CREATE_DEAL
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

    private function prepareDeals(array $deals): array
    {
        $dealsList = [];

        foreach ($deals as $deal) {
            $dealsList[] = [
                "id" => $deal->id,
                "deal_name" => $deal->Deal_Name,
                "stage" => $deal->Stage
            ];
        }

        return $dealsList;
    }

}
