<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\CrmActivities\CrmActivityMeetingController;
use App\Http\Controllers\Tesoreria\TesoreriaController;
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
Route::prefix('tesoreria')->group(function () {
    Route::post('/gastos', [TesoreriaController::class, 'storeUnclassifiedExpensese']);
});
Route::post('/acta/description', action: [CrmActivityMeetingController::class, 'updateMeeting']);
Route::post('/getAyudas', action: [ApiController::class, 'getayudas']);
Route::post('/updateAyudas/{id}', action: [ApiController::class, 'updateAyudas']);
Route::post('/updateMensajes', action: [ApiController::class, 'updateMensajes']);
