<?php

namespace App\Modules\Deal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Deal\Services\DealService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function getAll(Request $request, DealService $dealService): JsonResponse
    {
       return $dealService->getAll($request);
    }
}
