<?php

namespace App\Modules\Deal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Deal\Requests\DealRequest;
use App\Modules\Deal\Services\DealService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    private DealService $dealService;

    public function __construct()
    {
        $this->dealService = new DealService();
    }

    public function getAll(Request $request): JsonResponse
    {
       return $this->dealService->getAll($request);
    }

    public function create(DealRequest $request): JsonResponse
    {
        return $this->dealService->create($request);
    }
}
