<?php

namespace App\Modules\Token\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Token\Requests\GenerateTokenRequest;
use App\Modules\Token\Services\AccessTokenService;
use App\Modules\Token\Services\TokenService;
use Illuminate\Http\JsonResponse;

class TokenController extends Controller
{
    public function generateToken(
        GenerateTokenRequest $request,
        TokenService $tokenService
    ): JsonResponse
    {
        $data = $tokenService->save(
            AccessTokenService::generate($request->input('code'))
        );

        return response()->json(['data' => [
            $data
        ]]);
    }
}
