<?php

namespace App\Modules\Token\Services;

use App\Helpers\UrlList;
use App\Modules\Token\Models\Token;
use Illuminate\Support\Facades\Http;

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

        $token = Token::where('refresh_token', $refreshToken)->firsh();
        $token->access_token = $response->access_token;
        $token->save();

        return json_decode($response->body());
    }

}
