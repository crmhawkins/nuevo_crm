<?php

use App\Events\RecargarPagina;
use App\Http\Controllers\CrmActivities\CrmActivityMeetingController;
use App\Http\Controllers\Suppliers\SuppliersController;
use App\Http\Controllers\Tesoreria\CuadroController;
use App\Http\Controllers\To_do\To_doController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\Petitions\PetitionController;
use App\Http\Controllers\Budgets\BudgetController;
use App\Http\Controllers\Tasks\TasksController;
use App\Http\Controllers\Budgets\BudgetConceptsController;
use App\Http\Controllers\Contabilidad\CuentasContableController;
use App\Http\Controllers\Contabilidad\SubCuentasContableController;
use App\Http\Controllers\Contabilidad\SubCuentasHijoController;
use App\Http\Controllers\Contabilidad\SubGrupoContabilidadController;
use App\Http\Controllers\Contratos\ContratosController;
use App\Http\Controllers\Passwords\PasswordsController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Services\ServicesController;
use App\Http\Controllers\Services\ServicesCategoriesController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Tesoreria\TesoreriaController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dominios\DominiosController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\GrupoContabilidadController;
use App\Http\Controllers\Holiday\HolidayController;
use App\Http\Controllers\Holiday\AdminHolidaysController;
use App\Http\Controllers\Incidence\IncidenceController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\Nominas\NominasController;
use App\Http\Controllers\Statistics\StatisticsController;
use App\Http\Controllers\Users\DepartamentController;
use App\Http\Controllers\Users\PositionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::name('inicio')->get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Dashboard

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard/getDataTask', [DashboardController::class, 'getDataTask'])->name('dashboard.getDataTask');
Route::post('/dashboard/getTasksRefresh', [DashboardController::class, 'getTasksRefresh'])->name('dashboard.getTasksRefresh');
Route::post('/dashboard/setStatusTask', [DashboardController::class, 'setStatusTask'])->name('dashboard.setStatusTask');
Route::post('/dashboard/llamada', [DashboardController::class, 'llamada'])->name('llamada.store');


Route::post('/start-jornada', [DashboardController::class, 'startJornada'])->name('dashboard.startJornada');
Route::post('/end-jornada', [DashboardController::class, 'endJornada'])->name('dashboard.endJornada');
Route::post('/start-pause', [DashboardController::class, 'startPause'])->name('dashboard.startPause');
Route::post('/end-pause', [DashboardController::class, 'endPause'])->name('dashboard.endPause');

//Events(Eventos del to-do)
Route::post('/event/store', [EventController::class, 'store'])->name('event.store');
Route::post('/todos/store', [To_doController::class, 'store'])->name('todos.store');
Route::post('/todos/finish/{id}', [To_doController::class, 'finish'])->name('todos.finalizar');
Route::post('/todos/complete/{id}', [To_doController::class, 'complete'])->name('todos.completar');
Route::post('/message/store', [MessageController::class, 'store'])->name('message.store');
Route::post('/mark-as-read/{todoId}', [MessageController::class,'markAsRead']);


//Meetings(Reuniosnes)
Route::get('/meeting', [CrmActivityMeetingController::class, 'index'])->name('reunion.index');
Route::get('/meeting/create', [CrmActivityMeetingController::class, 'createMeetingFromAllUsers'])->name('reunion.create');
Route::get('/view-meeting/{id}', [CrmActivityMeetingController::class, 'viewMeeting'])->name('reunion.view');
Route::post('/meeting/store', [CrmActivityMeetingController::class, 'storeMeetingFromAllUsers'])->name('reunion.store');
Route::post('/meeting/alreadyRead/{id}', [CrmActivityMeetingController::class, 'alreadyRead'])->name('reunion.alreadyRead');
Route::post('/meeting/addComments/{id}', [CrmActivityMeetingController::class, 'addCommentsToMeeting'])->name('reunion.addComments');


//Holidays(Vacaciones users)
Route::get('/holidays', [HolidayController::class, 'index'])->name('holiday.index');
Route::get('/holidays/edit/{id}', [HolidayController::class, 'edit'])->name('holiday.edit');
Route::post('/holidays/store', [HolidayController::class, 'store'])->name('holiday.store');
Route::get('/holidays/create', [HolidayController::class, 'create'])->name('holiday.create');

//Holidays(Vacaciones Admin)
Route::get('/holidays/index', [AdminHolidaysController::class, 'index'])->name('holiday.admin.index');
Route::get('/holidays/admin-create', [AdminHolidaysController::class, 'create'])->name('holiday.admin.create');
Route::get('/holidays/store', [AdminHolidaysController::class, 'store'])->name('holiday.admin.store');
Route::get('/holidays/destroy', [AdminHolidaysController::class, 'destroy'])->name('holiday.admin.destroy');
Route::get('/holidays/admin-edit/{id}', [AdminHolidaysController::class, 'edit'])->name('holiday.admin.edit');
Route::post('/holidays/admin-update', [AdminHolidaysController::class, 'update'])->name('holiday.admin.update');
Route::get('/holidays/petitions', [AdminHolidaysController::class, 'usersPetitions'])->name('holiday.admin.petitions');
Route::get('/holidays/record', [AdminHolidaysController::class, 'addedRecord'])->name('holiday.admin.record');
Route::get('/holidays/history', [AdminHolidaysController::class, 'allHistory'])->name('holiday.admin.history');
Route::get('/holidays/managePetition/{id}', [AdminHolidaysController::class, 'managePetition'])->name('holiday.admin.managePetition');
Route::post('/holidays/acceptHolidays', [AdminHolidaysController::class, 'acceptHolidays'])->name('holiday.admin.acceptHolidays');
Route::post('/holidays/denyHolidays', [AdminHolidaysController::class, 'denyHolidays'])->name('holiday.admin.denyHolidays');


// Users (USUARIOS)
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/user/create', [UserController::class, 'create'])->name('users.create');
Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::post('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::get('/user/show/{id}', [UserController::class, 'show'])->name('users.show');
Route::post('/user/destroy', [UserController::class, 'destroy'])->name('users.delete');
Route::post('/user/avatar/{id}', [UserController::class, 'avatar'])->name('users.avatar');

// Clients (CLIENTES)
Route::get('/clients', [ClientController::class, 'index'])->name('clientes.index');
Route::get('/client/create', [ClientController::class, 'create'])->name('clientes.create');
Route::get('/client/edit/{id}', [ClientController::class, 'edit'])->name('clientes.edit');
Route::post('/client/store', [ClientController::class, 'store'])->name('clientes.store');
Route::post('/client/update/{id}', [ClientController::class, 'update'])->name('clientes.update');
Route::get('/client/show/{id}', [ClientController::class, 'show'])->name('clientes.show');
Route::post('/client/destroy', [ClientController::class, 'destroy'])->name('clientes.delete');
Route::post('/client/logo/{id}', [ClientController::class, 'logo'])->name('clientes.logo');
Route::get('/client/create-from-budget', [ClientController::class, 'createFromBudget'])->name('cliente.createFromBudget');
Route::post('/client/store-from-budget', [ClientController::class, 'storeFromBudget'])->name('cliente.storeFromBudget');
Route::get('/client/create-from-petition', [ClientController::class, 'createFromPetition'])->name('cliente.createFromPetition');
Route::post('/client/store-from-petition', [ClientController::class, 'storeFromPetition'])->name('cliente.storeFromPetition');
Route::post('/client/get-gestor', [ClientController::class, 'getGestorFromClient'])->name('cliente.getGestor');
Route::post('/client/get-contacts', [ClientController::class, 'getContactsFromClient'])->name('cliente.getContacts');
Route::post('/client/verificar-existente', [ClientController::class, 'verificarClienteExistente'])->name('cliente.verificarExistente');


//Proveedores
Route::get('/supplier', [SuppliersController::class, 'index'])->name('proveedores.index');
Route::get('/supplier/create', [SuppliersController::class, 'create'])->name('proveedores.create');
Route::get('/supplier/edit/{id}', [SuppliersController::class, 'edit'])->name('proveedores.edit');
Route::post('/supplier/store', [SuppliersController::class, 'store'])->name('proveedores.store');
Route::post('/supplier/update/{id}', [SuppliersController::class, 'update'])->name('proveedores.update');
Route::get('/supplier/show/{id}', [SuppliersController::class, 'show'])->name('proveedores.show');
Route::post('/supplier/destroy', [SuppliersController::class, 'destroy'])->name('proveedores.delete');

// Petition (PETICIONES)
Route::get('/petition', [PetitionController::class, 'index'])->name('peticion.index');
Route::get('/petition-for-user', [PetitionController::class, 'indexUser'])->name('peticion.indexUser');
Route::get('/petition/create', [PetitionController::class, 'create'])->name('peticion.create');
Route::get('/petition/edit/{id}', [PetitionController::class, 'edit'])->name('peticion.edit');
Route::post('/petition/store', [PetitionController::class, 'store'])->name('peticion.store');
Route::post('/budpetitionget/update/{id}', [PetitionController::class, 'update'])->name('peticion.update');
Route::post('/petition/destroy', [PetitionController::class, 'destroy'])->name('peticion.delete');



// Budgets (PRESUPUESTOS)
Route::get('/budgets', [BudgetController::class, 'index'])->name('presupuestos.index');
Route::get('/budgets-for-user', [BudgetController::class, 'indexUser'])->name('presupuestos.indexUser');
Route::get('/budget/create', [BudgetController::class, 'create'])->name('presupuesto.create');
Route::get('/budget/create-from-petition/{id}', [BudgetController::class, 'createFromPetition'])->name('presupuesto.createFromPetition');
Route::get('/budget/edit/{id}', [BudgetController::class, 'edit'])->name('presupuesto.edit');
Route::post('/budget/store', [BudgetController::class, 'store'])->name('presupuesto.store');
Route::post('/budget/update/{id}', [BudgetController::class, 'update'])->name('presupuesto.update');
Route::post('/budget/duplicate/{id}', [BudgetController::class, 'duplicate'])->name('presupuesto.duplicate');
Route::get('/budget/show/{id}', [BudgetController::class, 'show'])->name('presupuesto.show');
Route::post('/budget/destroy', [BudgetController::class, 'destroy'])->name('presupuesto.delete');
Route::post('/budget/logo/{id}', [BudgetController::class, 'logo'])->name('presupuesto.logo');
Route::get('/budget/create-from-project/{cliente}', [BudgetController::class, 'createFromProject'])->name('presupuesto.createFromProject');
Route::post('/budget/accept-budget/', [BudgetController::class, 'aceptarPresupuesto'])->name('presupuesto.aceptarPresupuesto');
Route::post('/budget/cancel-budget/', [BudgetController::class, 'cancelarPresupuesto'])->name('presupuesto.cancelarPresupuesto');
Route::post('/budget/generate-invoice', [BudgetController::class, 'generateInvoice'])->name('presupuesto.generarFactura');
Route::post('/budget/generate-partia-invoice', [BudgetController::class, 'generateInvoicePartial'])->name('presupuesto.generarFacturaParcial');
Route::post('/budget/generate-task', [BudgetController::class, 'createTask'])->name('presupuesto.generarTarea');
Route::post('/budget/generate-pdf', [BudgetController::class, 'generatePDF'])->name('presupuesto.generarPDF');
Route::post('/budgets-by-client', [BudgetController::class, 'getBudgetsByClientId']);
Route::post('/budgets-by-project', [BudgetController::class, 'getBudgetsByprojectId']);
Route::post('/budget-by-id', [BudgetController::class, 'getBudgetById']);
Route::get('/status-projects', [BudgetController::class, 'statusProjects'])->name('presupuestos.status');


// Budgets Concepts (CONCEPTOS DE PRESUPUESTOS)
Route::get('/budget-concepts/{budget}/create-type-own', [BudgetConceptsController::class, 'createTypeOwn'])->name('budgetConcepts.createTypeOwn');
Route::post('/budget-concepts/{budget}/store-type-own', [BudgetConceptsController::class, 'storeTypeOwn'])->name('budgetConcepts.storeTypeOwn');
Route::get('/budget-concepts/{budgetConcept}/edit-type-own', [BudgetConceptsController::class, 'editTypeOwn'])->name('budgetConcepts.editTypeOwn');
Route::post('/budget-concepts/{budgetConcept}/update-type-own', [BudgetConceptsController::class, 'updateTypeOwn'])->name('budgetConcepts.updateTypeOwn');
Route::get('/budget-concepts/{budgetConcept}/destroy-type-own', [BudgetConceptsController::class, 'destroyTypeOwn'])->name('budgetConcepts.destroyTypeOwn');

Route::get('/budget-concepts/{budget}/create-type-supplier', [BudgetConceptsController::class, 'createTypeSupplier'])->name('budgetConcepts.createTypeSupplier');
Route::post('/budget-concepts/{budget}/store-type-supplier', [BudgetConceptsController::class, 'storeTypeSupplier'])->name('budgetConcepts.storeTypeSupplier');
Route::get('/budget-concepts/{budgetConcept}/edit-type-supplier', [BudgetConceptsController::class, 'editTypeSupplier'])->name('budgetConcepts.editTypeSupplier');
Route::post('/budget-concepts/{budgetConcept}/update-type-supplier', [BudgetConceptsController::class, 'updateTypeSupplier'])->name('budgetConcepts.updateTypeSupplier');
Route::post('/budget-concepts/{budgetConcept}/destroy-type-supplier', [BudgetConceptsController::class, 'destroyTypeSupplier'])->name('budgetConcepts.destroyTypeSupplier');

Route::get('/budget-concepts/{categoryId}', [BudgetConceptsController::class, 'getServicesByCategory'])->name('budgetConcepts.getServicesByCategory');
Route::post('/budget-concepts/category-service', [BudgetConceptsController::class, 'getInfoByServices'])->name('budgetConcepts.getInfoByServices');
Route::post('/budget-concepts/delete', [BudgetConceptsController::class, 'deleteConceptsType'])->name('budgetConcepts.delete');
Route::post('/budget-concepts/discount-update', [BudgetConceptsController::class, 'discountUpdate'])->name('budgetConcepts.discountUpdate');

Route::post('/budget-concept-supplier/saveOrderForSend', [BudgetConceptsController::class, 'saveOrderForSend'])->name('budgetConcepts.saveOrderForSend');
Route::post('/budget-concept-supplier/generatePurchaseOrder/{id}', [BudgetConceptsController::class, 'generatePurchaseOrder'])->name('budgetConcepts.generatePurchaseOrder');
Route::get('/budget-concept-supplier/preview-pdf/{id}', [BudgetConceptsController::class, 'generatePDF'])->name('purchase_order.purchaseOrderPDF');

// Projects (CAMPAÑAS)
Route::get('/projects', [ProjectController::class, 'index'])->name('campania.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->name('campania.create');
Route::get('/projects/edit/{id}', [ProjectController::class, 'show'])->name('campania.show');
Route::get('/projects/show/{id}', [ProjectController::class, 'edit'])->name('campania.edit');
Route::get('/projects/{cliente}/create-from-budget', [ProjectController::class, 'createFromBudget'])->name('campania.createFromBudget');
Route::get('/projects/{cliente}/create-from-budget/{petitionid}', [ProjectController::class, 'createFromBudgetAndPetition'])->name('campania.createFromBudgetAndPetition');
Route::post('/projects/store', [ProjectController::class, 'store'])->name('campania.store');
Route::post('/projects/update/{id}', [ProjectController::class, 'update'])->name('campania.update');
Route::post('/projects/destroy', [ProjectController::class, 'destroy'])->name('campania.delete');
Route::post('/projects/store-from-budget', [ProjectController::class, 'storeFromBudget'])->name('campania.storeFromBudget');
Route::post('/projects/update-from-window', [ProjectController::class, 'updateFromWindow'])->name('campania.updateFromWindow');
Route::post('/projects-from-client', [ProjectController::class, 'postProjectsFromClient'])->name('campania.postProjectsFromClient');
Route::post('/project-by-id', [ProjectController::class, 'getProjectById']);

// Services (SERVICIOS)
Route::get('/services', [ServicesController::class, 'index'])->name('servicios.index');
Route::get('/services/create', [ServicesController::class, 'create'])->name('servicios.create');
Route::get('/services-show/{id}', [ServicesController::class, 'show'])->name('servicios.show');
Route::get('/services/edit/{id}', [ServicesController::class, 'edit'])->name('servicios.edit');
Route::post('/services/store', [ServicesController::class, 'store'])->name('servicios.store');
Route::post('/services/update/{id}', [ServicesController::class, 'update'])->name('servicios.update');
Route::post('/services/destroy', [ServicesController::class, 'destroy'])->name('servicios.delete');

// Services Categories (CATEGORIA DE SERVICIOS)
Route::get('/services-categories', [ServicesCategoriesController::class, 'index'])->name('serviciosCategoria.index');
Route::get('/services-categories/create', [ServicesCategoriesController::class, 'create'])->name('serviciosCategoria.create');
Route::get('/services-categories/edit/{id}', [ServicesCategoriesController::class, 'edit'])->name('serviciosCategoria.edit');
Route::post('/services-categories/store', [ServicesCategoriesController::class, 'store'])->name('serviciosCategoria.store');
Route::post('/services-categories/update/{id}', [ServicesCategoriesController::class, 'update'])->name('serviciosCategoria.update');
Route::post('/services-categories/destroy', [ServicesCategoriesController::class, 'destroy'])->name('serviciosCategoria.delete');

// Suppliers (PROVEEDORES)
Route::get('/suppliers/{supplier}/get-supplier', [App\Http\Controllers\Suppliers\SuppliersController::class, 'getSupplier'])->name('proveedores.getSupplier');

// Invoice (FACTURAS)
Route::get('/invoices', [InvoiceController::class, 'index'])->name('facturas.index');
Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('factura.create');
Route::get('/invoice/edit/{id}', [InvoiceController::class, 'edit'])->name('factura.edit');
Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('factura.store');
Route::post('/invoice/update/{id}', [InvoiceController::class, 'update'])->name('factura.update');
Route::get('/invoice/show/{id}', [InvoiceController::class, 'show'])->name('factura.show');
Route::post('/invoice/destroy', [InvoiceController::class, 'destroy'])->name('factura.delete');
Route::post('/invoice/paid-invoice', [InvoiceController::class, 'cobrarFactura'])->name('factura.cobrada');
Route::post('/invoice/generate-pdf', [InvoiceController::class, 'generatePDF'])->name('factura.generarPDF');
Route::post('/invoice/rectify', [InvoiceController::class, 'rectificateInvoice'])->name('factura.rectificada');

// Task (TAREAS)
Route::get('/tasks', [TasksController::class, 'index'])->name('tareas.index');
Route::get('/tasks/cola-trabajo', [TasksController::class, 'cola'])->name('tareas.cola');
Route::get('/tasks/revision', [TasksController::class, 'revision'])->name('tareas.revision');
Route::get('/tasks/asignar', [TasksController::class, 'asignar'])->name('tareas.asignar');
Route::get('/task/create', [TasksController::class, 'create'])->name('tarea.create');
Route::get('/task/edit/{id}', [TasksController::class, 'edit'])->name('tarea.edit');
Route::post('/task/store', [TasksController::class, 'store'])->name('tarea.store');
Route::post('/task/update/{id}', [TasksController::class, 'update'])->name('tarea.update');
Route::get('/task/show/{id}', [TasksController::class, 'show'])->name('tarea.show');
Route::post('/task/destroy', [TasksController::class, 'destroy'])->name('tarea.delete');

// Dominios
Route::get('/dominios', [DominiosController::class, 'index'])->name('dominios.index');
Route::get('/dominios/create', [DominiosController::class, 'create'])->name('dominios.create');
Route::get('/dominios/edit/{id}', [DominiosController::class, 'edit'])->name('dominios.edit');
Route::post('/dominios/store', [DominiosController::class, 'store'])->name('dominios.store');
Route::post('/dominios/update/{id}', [DominiosController::class, 'update'])->name('dominios.update');
Route::post('/dominios/destroy', [DominiosController::class, 'destroy'])->name('dominios.delete');

//Nominas
Route::get('/nominas', [NominasController::class, 'index'])->name('nominas.index');
Route::get('/nominas/{id}', [NominasController::class, 'indexUser'])->name('nominas.index_user');
Route::get('/nominas/create', [NominasController::class, 'create'])->name('nominas.create');
Route::get('/nominas/edit/{id}', [NominasController::class, 'edit'])->name('nominas.edit');
Route::get('/nominas/show/{id}', [NominasController::class, 'show'])->name('nominas.show');
Route::post('/nominas/store', [NominasController::class, 'store'])->name('nominas.store');
Route::post('/nominas/update/{id}', [NominasController::class, 'update'])->name('nominas.update');
Route::post('/nominas/destroy', [NominasController::class, 'destroy'])->name('nominas.delete');

//Departamentos
Route::get('/departament', [DepartamentController::class, 'index'])->name('departamento.index');
Route::get('/departament/create', [DepartamentController::class, 'create'])->name('departamento.create');
Route::get('/departament/edit/{id}', [DepartamentController::class, 'edit'])->name('departamento.edit');
Route::post('/departament/store', [DepartamentController::class, 'store'])->name('departamento.store');
Route::post('/departament/update/{id}', [DepartamentController::class, 'update'])->name('departamento.update');
Route::post('/departament/destroy', [DepartamentController::class, 'destroy'])->name('departamento.delete');

//Cargos
Route::get('/position', [PositionController::class, 'index'])->name('cargo.index');
Route::get('/position/create', [PositionController::class, 'create'])->name('cargo.create');
Route::get('/position/edit/{id}', [PositionController::class, 'edit'])->name('cargo.edit');
Route::post('/position/store', [PositionController::class, 'store'])->name('cargo.store');
Route::post('/position/update/{id}', [PositionController::class, 'update'])->name('cargo.update');
Route::post('/position/destroy', [PositionController::class, 'destroy'])->name('cargo.delete');

//Contratos
Route::get('/contratos', [ContratosController::class, 'index'])->name('contratos.index');
Route::get('/contratos/{id}', [ContratosController::class, 'indexUser'])->name('contratos.index_user');
Route::get('/contratos/create', [ContratosController::class, 'create'])->name('contratos.create');
Route::get('/contratos/edit/{id}', [ContratosController::class, 'edit'])->name('contratos.edit');
Route::get('/contratos/show/{id}', [ContratosController::class, 'show'])->name('contratos.show');
Route::post('/contratos/store', [ContratosController::class, 'store'])->name('contratos.store');
Route::post('/contratos/update/{id}', [ContratosController::class, 'update'])->name('contratos.update');
Route::post('/contratos/destroy', [ContratosController::class, 'destroy'])->name('contratos.delete');

// Contraseñas
Route::get('/passwords', [PasswordsController::class, 'index'])->name('passwords.index');
Route::get('/passwords/create', [PasswordsController::class, 'create'])->name('passwords.create');
Route::get('/passwords/edit/{id}', [PasswordsController::class, 'edit'])->name('passwords.edit');
Route::post('/passwords/store', [PasswordsController::class, 'store'])->name('passwords.store');
Route::post('/passwords/update/{id}', [PasswordsController::class, 'update'])->name('passwords.update');
Route::post('/passwords/destroy', [PasswordsController::class, 'destroy'])->name('passwords.delete');

// web.php
Route::post('/save-theme-preference', [UserController::class, 'saveThemePreference'])->name('saveThemePreference');

// Portal Clientes
Route::get('/portal/dashboard', [App\Http\Controllers\Portal\PortalClientesController::class, 'dashboard'])->name('portal.dashboard');
Route::get('/portal/presupuestos', [App\Http\Controllers\Portal\PortalClientesController::class, 'presupuestos'])->name('portal.presupuestos');
Route::get('/portal/facturas', [App\Http\Controllers\Portal\PortalClientesController::class, 'facturas'])->name('portal.facturas');

// Ingresos (TESORERIA)
Route::get('/ingresos', [TesoreriaController::class, 'indexIngresos'])->name('ingresos.index');
Route::get('/ingreso/create', [TesoreriaController::class, 'createIngresos'])->name('ingreso.create');
Route::get('/ingreso/edit/{id}', [TesoreriaController::class, 'editIngresos'])->name('ingreso.edit');
Route::get('/ingreso/show/{id}', [TesoreriaController::class, 'showIngresos'])->name('ingreso.show');
Route::post('/ingreso/store', [TesoreriaController::class, 'storeIngresos'])->name('ingreso.store');
Route::post('/ingreso/update/{id}', [TesoreriaController::class, 'updateIngresos'])->name('ingreso.update');
Route::post('/ingreso/destroy', [TesoreriaController::class, 'destroyIngresos'])->name('ingreso.delete');

// Gastos (TESORERIA)
Route::get('/gastos', [TesoreriaController::class, 'indexGastos'])->name('gastos.index');
Route::get('/gasto/create', [TesoreriaController::class, 'createGastos'])->name('gasto.create');
Route::get('/gasto/edit/{id}', [TesoreriaController::class, 'editGastos'])->name('gasto.edit');
Route::get('/gasto/show/{id}', [TesoreriaController::class, 'showGastos'])->name('gasto.show');
Route::post('/gasto/store', [TesoreriaController::class, 'storeGastos'])->name('gasto.store');
Route::post('/gasto/update/{id}', [TesoreriaController::class, 'updateGastos'])->name('gasto.update');
Route::post('/gasto/destroy', [TesoreriaController::class, 'destroyGastos'])->name('gasto.delete');

// Gastos asociados (TESORERIA)
Route::get('/gastos-asociados', [TesoreriaController::class, 'indexAssociatedExpenses'])->name('gastos-asociados.index');
Route::get('/gasto-asociado/create', [TesoreriaController::class, 'createAssociatedExpenses'])->name('gasto-asociado.create');
Route::get('/gasto-asociado/edit/{id}', [TesoreriaController::class, 'editAssociatedExpenses'])->name('gasto-asociado.edit');
Route::post('/gasto-asociado/store', [TesoreriaController::class, 'storeAssociatedExpenses'])->name('gasto-asociado.store');
Route::post('/gasto-asociado/update/{id}', [TesoreriaController::class, 'updateAssociatedExpenses'])->name('gasto-asociado.update');
Route::post('/gasto-asociado/destroy', [TesoreriaController::class, 'destroyAssociatedExpenses'])->name('gasto-asociado.delete');

Route::get('/incidencias', [IncidenceController::class, 'index'])->name('incidencias.index');
Route::get('/incidencias/create', [IncidenceController::class, 'create'])->name('incidencias.create');
Route::get('/incidencias/edit/{id}', [IncidenceController::class, 'edit'])->name('incidencias.edit');
Route::post('/incidencias/store', [IncidenceController::class, 'storeAssociatedExpenses'])->name('incidencias.store');
Route::post('/incidencias/update/{id}', [IncidenceController::class, 'updateAssociatedExpenses'])->name('incidencias.update');
Route::post('/incidencias/destroy', [IncidenceController::class, 'destroyAssociatedExpenses'])->name('incidencias.delete');

// Gastos sin clasificar (TESORERIA)
Route::get('/gastos-sin-clasificar', [TesoreriaController::class, 'indexUnclassifiedExpensese'])->name('gastos-sin-clasificar.index');
Route::get('/gastos-sin-clasificar/edit/{id}', [TesoreriaController::class, 'editUnclassifiedExpensese'])->name('gasto-sin-clasificar.edit');
Route::post('/gastos-sin-clasificar/update/{id}', [TesoreriaController::class, 'updateUnclassifiedExpensese'])->name('gasto-sin-clasificar.update');
Route::post('/gastos-sin-clasificar/destroy', [TesoreriaController::class, 'destroyUnclassifiedExpensese'])->name('gastos-sin-clasificar.delete');

// Treasury(Cuadro)
Route::get('/treasury', [CuadroController::class,'index'])->name('admin.treasury.index');
Route::get('/treasury/{anio}/{mes}/getMonthYear',[CuadroController::class,'getMonthYear'])->name('admin.treasury.getMonthYear');
Route::post('/treasury/SaveInvoice',[CuadroController::class,'SaveInvoice'])->name('admin.treasury.SaveInvoice');
Route::post('/treasury/SaveInvoiceData',[CuadroController::class,'SaveInvoiceData'])->name('admin.treasury.SaveInvoiceData');
Route::post('/treasury/ChangeInvoiceStatus',[CuadroController::class,'ChangeInvoiceStatus'])->name('admin.treasury.ChangeInvoiceStatus');
Route::post('/treasury/getInvoices',[CuadroController::class,'getInvoices'])->name('admin.treasury.getInvoices');
Route::post('/treasury/saveDateContabilidad',[CuadroController::class,'saveDateContabilidad'])->name('admin.treasury.saveDateContabilidad');
Route::post('/treasury/getIngresos',[CuadroController::class,'getIngresos'])->name('admin.treasury.getIngresos');
Route::post('/treasury/getGastos',[CuadroController::class,'getGastos'])->name('admin.treasury.getGastos');
Route::post('/treasury/getGastosAsociados',[CuadroController::class,'getGastosAsociados'])->name('admin.treasury.getGastosAsociados');
Route::get('/treasury/{year}', [CuadroController::class,'indexYear'])->name('admin.treasury.indexYear');
// Configuracion


Route::get('/statistics', [StatisticsController::class, 'index'])->name('estadistica.index');

Route::get('/configuracion', [SettingsController::class, 'index'])->name('configuracion.index');
Route::post('/configuracion/update/{id}', [SettingsController::class, 'update'])->name('configuracion.update');
Route::post('/configuracion/store', [SettingsController::class, 'store'])->name('configuracion.store');


Route::get('/cuentas-contables', [CuentasContableController::class, 'index'])->name('cuentasContables.index');
Route::get('/cuentas-contables/create', [CuentasContableController::class, 'create'])->name('cuentasContables.create');
Route::post('/cuentas-contables/store', [CuentasContableController::class, 'store'])->name('cuentasContables.store');
Route::get('/cuentas-contables/{id}/edit', [CuentasContableController::class, 'edit'])->name('cuentasContables.edit');
Route::post('/cuentas-contables/updated', [CuentasContableController::class, 'updated'])->name('cuentasContables.updated');
Route::delete('/cuentas-contables/destroy/{id}', [CuentasContableController::class, 'destroy'])->name('cuentasContables.destroy');

Route::get('/cuentas-contables/get-cuentas', [CuentasContableController::class, 'getCuentasByDataTables'])->name('cuentasContables.getClients');

// Sub-Cuentas Contables
Route::get('/sub-cuentas-contables', [SubCuentasContableController::class, 'index'])->name('subCuentasContables.index');
Route::get('/sub-cuentas-contables/create', [SubCuentasContableController::class, 'create'])->name('subCuentasContables.create');
Route::post('/sub-cuentas-contables/store', [SubCuentasContableController::class, 'store'])->name('subCuentasContables.store');
Route::get('/sub-cuentas-contables/{id}/edit', [SubCuentasContableController::class, 'edit'])->name('subCuentasContables.edit');
Route::post('/sub-cuentas-contables/updated', [SubCuentasContableController::class, 'updated'])->name('subCuentasContables.updated');
Route::delete('/sub-cuentas-contables/destroy/{id}', [SubCuentasContableController::class, 'destroy'])->name('subCuentasContables.destroy');

// Sub-Cuentas Hijas Contables
Route::get('/sub-cuentas-hijas-contables', [SubCuentasHijoController::class, 'index'])->name('subCuentasHijaContables.index');
Route::get('/sub-cuentas-hijas-contables/create', [SubCuentasHijoController::class, 'create'])->name('subCuentasHijaContables.create');
Route::post('/sub-cuentas-hijas-contables/store', [SubCuentasHijoController::class, 'store'])->name('subCuentasHijaContables.store');
Route::get('/sub-cuentas-hijas-contables/{id}/edit', [SubCuentasHijoController::class, 'edit'])->name('subCuentasHijaContables.edit');
Route::post('/sub-cuentas-hijas-contables/updated', [SubCuentasHijoController::class, 'updated'])->name('subCuentasHijaContables.updated');
Route::delete('/sub-cuentas-hijas-contables/destroy/{id}', [SubCuentasHijoController::class, 'destroy'])->name('subCuentasHijaContables.destroy');

// Grupos Contables
Route::get('/grupo-contable', [GrupoContabilidadController::class, 'index'])->name('grupoContabilidad.index');
Route::get('/grupo-contable/create', [GrupoContabilidadController::class, 'create'])->name('grupoContabilidad.create');
Route::post('/grupo-contable/store', [GrupoContabilidadController::class, 'store'])->name('grupoContabilidad.store');
Route::get('/grupo-contable/{id}/edit', [GrupoContabilidadController::class, 'edit'])->name('grupoContabilidad.edit');
Route::post('/grupo-contable/updated', [GrupoContabilidadController::class, 'updated'])->name('grupoContabilidad.updated');
Route::delete('/grupo-contable/destroy/{id}', [GrupoContabilidadController::class, 'destroy'])->name('grupoContabilidad.destroy');

// Sub-Grupos Contables
Route::get('/sub-grupo-contable', [SubGrupoContabilidadController::class, 'index'])->name('subGrupoContabilidad.index');
Route::get('/sub-grupo-contable/create', [SubGrupoContabilidadController::class, 'create'])->name('subGrupoContabilidad.create');
Route::post('/sub-grupo-contable/store', [SubGrupoContabilidadController::class, 'store'])->name('subGrupoContabilidad.store');
Route::get('/sub-grupo-contable/{id}/edit', [SubGrupoContabilidadController::class, 'edit'])->name('subGrupoContabilidad.edit');
Route::post('/sub-grupo-contable/updated', [SubGrupoContabilidadController::class, 'updated'])->name('subGrupoContabilidad.updated');
Route::delete('/sub-grupo-contable/destroy/{id}', [SubGrupoContabilidadController::class, 'destroy'])->name('subGrupoContabilidad.destroy');

Route::post('/save-order', [BudgetController::class, 'saveOrder'])->name('save.order');


// Kit Digital
Route::get('/kit-digital', [KitDigitalController::class, 'index'])->name('kitDigital.index');

});



Route::get('/ruta-prueba', function () {
    event(new RecargarPagina(50));
    return view('portal.dashboard');
});
