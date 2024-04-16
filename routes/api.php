<?php

use App\Http\Controllers\NafathController;
use App\Http\Controllers\NetworkLayer;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::get('/testing-network-layer', [NetworkLayer::class, 'networkCall']);

Route::post('/nafath/create-request', [NafathController::class, 'createRequest']);
Route::post('/nafath/request-status', [NafathController::class, 'requestStatus']);
Route::post('/nafath/get-detail-by-national-id', [NafathController::class, 'getJwtByNationalId']);
Route::get('/nafath/get-jwk', [NafathController::class, 'getJwk']);
Route::get('/nafath/callback', [NafathController::class, 'nafathCallBackURL']);

