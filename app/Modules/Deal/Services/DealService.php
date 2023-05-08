<?php

namespace App\Modules\Deal\Services;

use App\Helpers\UrlList;
use App\Modules\Token\Services\AccessTokenService;
use App\Services\RequestServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
