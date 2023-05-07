<?php

namespace App\Modules\Account\Controllers;

use App\Helpers\Constants;
use App\Modules\Account\Requests\AccountRequest;
use App\Modules\Account\Services\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Constants
{

    public function create(AccountRequest $request): JsonResponse
    {
        $accountService = new AccountService();
        return $accountService->create($request);
    }

}
