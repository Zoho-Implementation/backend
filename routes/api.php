<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/account', [\App\Modules\Account\Controllers\AccountController::class, 'getAll']);
Route::post('/account', [\App\Modules\Account\Controllers\AccountController::class, 'create']);

Route::get('/deals', [\App\Modules\Deal\Controllers\DealController::class, 'getAll']);

Route::post('/token/generate', [\App\Modules\Token\Controllers\TokenController::class, 'generateToken']);
