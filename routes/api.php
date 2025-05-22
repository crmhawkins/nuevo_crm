<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\SalvineitorController;
use App\Http\Controllers\CrmActivities\CrmActivityMeetingController;
use App\Http\Controllers\Tesoreria\TesoreriaContabilizarIa;
use App\Http\Controllers\Tesoreria\TesoreriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AutomatizacionKit\KitPagadosController;
use App\Http\Controllers\Suite\SuiteController;


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

Route::get('/salvineitor', action: [SalvineitorController::class, 'index']);
Route::post('/suite/get-users', [SuiteController::class, 'login'])->name('suite.software_login');

Route::post('/tesoreria/contabilizar-ia/upload', [TesoreriaContabilizarIa::class, 'upload']);
Route::post('/tesoreria/multi-ingreso', [TesoreriaController::class, 'multiIngreso'])->name('tesoreria.multi-ingreso');
