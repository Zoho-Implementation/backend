<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Requests\AccountRequest;
use App\Modules\Account\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    private AccountService $accountService;

    public function __construct()
    {
        $this->accountService = new AccountService();
    }

    public function getAll(Request $request): JsonResponse
    {
        return $this->accountService->getAll($request);
    }

    public function create(AccountRequest $request): JsonResponse
    {
        return $this->accountService->create($request);
    }

}
