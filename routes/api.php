<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\SalvineitorController;
use App\Http\Controllers\Autoseo\AutoseoJsonController;
use App\Http\Controllers\Autoseo\AutoseoReports;
use App\Http\Controllers\CrmActivities\CrmActivityMeetingController;
use App\Http\Controllers\Plataforma\PlataformaWhatsappController;
use App\Http\Controllers\Tesoreria\TesoreriaContabilizarIa;
use App\Http\Controllers\Tesoreria\TesoreriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AutomatizacionKit\KitPagadosController;
use App\Http\Controllers\Suite\SuiteController;
use App\Http\Controllers\Suite\SuiteUploadController;
use App\Http\Controllers\DonDominio\DonDominioController;

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
Route::post('/suite/upload', [SuiteUploadController::class, 'upload'])->name('suite.upload');
Route::get('/suite/archivos/{type}', [SuiteUploadController::class, 'listarArchivos'])->name('suite.archivos');
Route::get('/suite/descargar', [SuiteUploadController::class, 'descargarArchivo'])->name('suite.descargar');

Route::post('/tesoreria/contabilizar-ia/upload', [TesoreriaContabilizarIa::class, 'upload']);
Route::post('/tesoreria/multi-ingreso', [TesoreriaController::class, 'multiIngreso'])->name('tesoreria.multi-ingreso');

Route::get('/autoseo/api', function () {
    return \App\Models\Autoseo\Autoseo::all()->toJson();
});

Route::get('/autoseo/json/last', [AutoseoJsonController::class, 'getLastJson'])->name('autoseo.json.last');
Route::get('/autoseo/json/storage', [AutoseoJsonController::class, 'getJsonStorage'])->name('autoseo.json.storage');
Route::post('/autoseo/json/upload/competencia', [AutoseoJsonController::class, 'uploadJsonCompetencia'])->name('autoseo.json.upload.competencia');
Route::post('/autoseo/json/upload', [AutoseoJsonController::class, 'uploadJson'])->name('autoseo.json.upload');
Route::get('/autoseo/json/{field}/{id}', [AutoseoJsonController::class, 'download'])->name('autoseo.json.download');
// Route::post('/autoseo/json/upload/{field}/{id}', [AutoseoJsonController::class, 'upload'])->name('autoseo.json.upload');

Route::get('/get/info/{domain}', [DonDominioController::class, 'getInfoDomain'])->name('getInfoDomain');
Route::get('/check/domain/{domain}', [DonDominioController::class, 'checkDomain'])->name('checkDomain');
Route::get('/check/balance', [DonDominioController::class, 'getBalance'])->name('checkBalance');

Route::post('/update/dns', [DonDominioController::class, 'updateDnsRecords'])->name('updateDnsRecords');
Route::post('/change/dns', [DonDominioController::class, 'changeDnsRecords'])->name('changeDnsRecords');
Route::post('/create/subdomain', [DonDominioController::class, 'createSubdomain'])->name('createSubdomain');
Route::post('/register/domain', [DonDominioController::class, 'registerDomain'])->name('registerDomain');

Route::post('/plataforma/store-log', [PlataformaWhatsappController::class, 'storeLog'])->name('storeLog');
Route::post('/plataforma/store-msg', [PlataformaWhatsappController::class, 'storeMsg'])->name('storeMsg');

Route::post('/autoseo/reports/login/', [AutoseoReports::class, 'login'])->name('autoseo.reports.login');
Route::post('/autoseo/reports/upload', [AutoseoReports::class, 'upload'])->name('autoseo.reports.upload');