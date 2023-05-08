<?php

namespace App\Modules\Token\Services;

use App\Helpers\Constants;
use App\Modules\Token\Exceptions\TokenException;
use App\Modules\Token\Models\Token;

class TokenService
{
    /**
     * @throws TokenException
     */
    public function save(object $request): array
    {
        if (isset($request->error)) {
            return ['error_message' => $request->error];
        }
        $token = new Token();
        $token->access_token = $request->access_token;
        $token->refresh_token = $request->refresh_token;
        $token->save();

        return ['access_code', $request->access_token];
    }
}
