<?php

namespace App\Modules\Token\Services;

use App\Helpers\UrlList;
use App\Modules\Token\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AccessTokenService
{
    const AUTHORIZATION_CODE = 'authorization_code';
    const REFRESH_TOKEN = 'refresh_token';

    public static function generate(string $code)
    {
        $response = Http::asForm()
            ->post(
                env('ZOHO_ACCOUNTS_DOMAIN') . UrlList::GENERATE_ACCESS_TOKEN,
                [
                    'client_id' => env('ZOHO_CLIENT_ID'),
                    'client_secret' => env('ZOHO_CLIENT_SECRET_KEY'),
                    'redirect_uri' => env('ZOHO_REDIRECT_URL'),
                    'code' => $code,
                    'grant_type' => self::AUTHORIZATION_CODE
                ]);
        return json_decode($response->body());
    }

    public static function refresh(string $refreshToken)
    {
        $response = Http::asForm()
            ->post(
                env('ZOHO_ACCOUNTS_DOMAIN') . UrlList::REFRESH_ACCESS_TOKEN,
                [
                    'client_id' => env('ZOHO_CLIENT_ID'),
                    'client_secret' => env('ZOHO_CLIENT_SECRET_KEY'),
                    'refresh_token' => $refreshToken,
                    'grant_type' => self::REFRESH_TOKEN
                ]);

        $data = $response->json();
        $token = Token::where('refresh_token', $refreshToken)->first();
        $token->access_token = $data['access_token'];
        $token->save();

        return json_decode($response->body());
    }

    public static function refreshWithCallback(
        string $token,
        Request $request,
        callable $callback
    ) {
        $currentToken = Token::where("access_token", $token)->first();
        $newToken = AccessTokenService::refresh($currentToken->refresh_token);
        $request->headers->set("Authorization", "Bearer $newToken->access_token");
        Session::put('token', $newToken->access_token);
        return call_user_func($callback, $newToken->access_token, $request);
    }

}
