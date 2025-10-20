<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\SalvineitorController;
use App\Http\Controllers\Autoseo\AutoseoJsonController;
use App\Http\Controllers\Autoseo\AutoseoReports;
use App\Http\Controllers\Autoseo\AutoseoAdvancedReports;
use App\Http\Controllers\Autoseo\AutoseoScheduleController;
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
Route::post('/autoseo/reports/generate-json-only', [AutoseoReports::class, 'generateJsonOnlyReport'])->name('autoseo.reports.generate.json.only');
Route::post('/autoseo/reports/generate-advanced', [AutoseoAdvancedReports::class, 'generateAdvancedReport'])->name('autoseo.reports.generate.advanced');
Route::post('/autoseo/upload-and-generate-report', [AutoseoJsonController::class, 'uploadJsonAndGenerateReport'])->name('autoseo.upload.and.generate');

// Endpoints para AutoSEO automático
Route::get('/autoseo/seotoday', [AutoseoScheduleController::class, 'getSeoToday'])->name('autoseo.seotoday');
Route::get('/autoseo/servicios/{id}', [AutoseoScheduleController::class, 'getServicios'])->name('autoseo.servicios');
Route::post('/autoseo/programar', [AutoseoScheduleController::class, 'programarCliente'])->name('autoseo.programar');
Route::post('/autoseo/actualizar-estado', [AutoseoScheduleController::class, 'actualizarEstado'])->name('autoseo.actualizar.estado');
Route::post('/autoseo/guardar-servicios', [AutoseoScheduleController::class, 'guardarServicios'])->name('autoseo.guardar.servicios');

// Endpoints para gestión de estado de programaciones SEO
Route::post('/autoseo/programacion/cambiar-estado', [\App\Http\Controllers\Api\SeoProgramacionController::class, 'cambiarEstado'])->name('autoseo.programacion.cambiar.estado');
Route::get('/autoseo/programacion/listar', [\App\Http\Controllers\Api\SeoProgramacionController::class, 'listar'])->name('autoseo.programacion.listar');
Route::get('/autoseo/priority', [\App\Http\Controllers\Api\SeoProgramacionController::class, 'getPriority'])->name('autoseo.programacion.priority');



// TOOLS
Route::get('/get-clientes', action: [ApiController::class, 'getClientes']);
Route::get('/get-clientes-contactos', action: [ApiController::class, 'getClientesContactos']);

// AI AGENT TOOLS
Route::post('/buscar-cliente', action: [AIController::class, 'buscarCliente']);
Route::post('/get-cliente-resumen', action: [AIController::class, 'getClienteResumen']);
Route::post('/get-producto-precio', action: [AIController::class, 'getProductoPrecio']);
Route::post('/get-clientes-contactos', action: [AIController::class, 'getClientesContactos']);
Route::post('/buscar-producto', action: [AIController::class, 'buscarProducto']);

// RUTAS PARA ELEVEN LABS (sin autenticación para el agente)
    Route::prefix('eleven-labs')->group(function () {
        // Citas
        Route::get('/citas-disponibles', [\App\Http\Controllers\Api\ElevenLabsController::class, 'getCitasDisponibles']);
        Route::post('/agendar-cita', [\App\Http\Controllers\Api\ElevenLabsController::class, 'agendarCita']);
        
        // Peticiones
        Route::post('/crear-peticion', [\App\Http\Controllers\Api\ElevenLabsController::class, 'crearPeticion']);
        
        // Datos de referencia
        Route::get('/gestores', [\App\Http\Controllers\Api\ElevenLabsController::class, 'getGestores']);
        Route::get('/clientes', [\App\Http\Controllers\Api\ElevenLabsController::class, 'getClientes']);
        Route::get('/proyectos', [\App\Http\Controllers\Api\ElevenLabsController::class, 'getProyectos']);
        
        // Gestión de clientes
        Route::get('/buscar-cliente', [\App\Http\Controllers\Api\ElevenLabsController::class, 'buscarCliente']);
        Route::post('/crear-cliente', [\App\Http\Controllers\Api\ElevenLabsController::class, 'crearCliente']);
    
        // Obtener citas existentes
        Route::get('/citas', [\App\Http\Controllers\Api\ElevenLabsController::class, 'getCitas']);

        // Obtener información del día de hoy
        Route::get('/dia-hoy', [\App\Http\Controllers\Api\ElevenLabsController::class, 'getDiaHoy']);

        // Presupuestos
        Route::post('/crear-presupuesto', [\App\Http\Controllers\Api\ElevenLabsController::class, 'crearPresupuesto']);
        Route::post('/enviar-presupuesto-pdf', [\App\Http\Controllers\Api\ElevenLabsController::class, 'enviarPresupuestoPDF']);
    });

