<?php

use App\Events\RecargarPagina;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\Petitions\PetitionController;
use App\Http\Controllers\Budgets\BudgetController;
use App\Http\Controllers\Tasks\TasksController;
use App\Http\Controllers\Budgets\BudgetConceptsController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Services\ServicesController;
use App\Http\Controllers\Services\ServicesCategoriesController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Tesoreria\TesoreriaController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\Holiday\HolidayController;
use App\Http\Controllers\Holiday\AdminHolidaysController;

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
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/start-jornada', [DashboardController::class, 'startJornada'])->name('dashboard.startJornada');
Route::post('/end-jornada', [DashboardController::class, 'endJornada'])->name('dashboard.endJornada');
Route::post('/start-pause', [DashboardController::class, 'startPause'])->name('dashboard.startPause');
Route::post('/end-pause', [DashboardController::class, 'endPause'])->name('dashboard.endPause');

//Events(Eventos del to-do)
Route::post('/event/store', [EventController::class, 'store'])->name('event.store');

//Holidays(Vacaciones users)
Route::get('/holidays', [HolidayController::class, 'index'])->name('holiday.index');
Route::get('/holidays/edit/{id}', [HolidayController::class, 'edit'])->name('holiday.edit');
Route::post('/holidays/store', [HolidayController::class, 'store'])->name('holiday.store');
Route::get('/holidays/create', [HolidayController::class, 'create'])->name('holiday.create');

//Holidays(Vacaciones Admin)
Route::get('/holidays/index', [AdminHolidaysController::class, 'index'])->name('holiday.admin.index');
Route::get('/holidays/create', [AdminHolidaysController::class, 'create'])->name('holiday.admin.create');
Route::get('/holidays/store', [AdminHolidaysController::class, 'store'])->name('holiday.admin.store');
Route::get('/holidays/destroy', [AdminHolidaysController::class, 'destroy'])->name('holiday.admin.destroy');
Route::get('/holidays/admin-edit/{id}', [AdminHolidaysController::class, 'edit'])->name('holiday.admin.edit');
Route::post('/holidays/admin-update', [AdminHolidaysController::class, 'update'])->name('holiday.admin.update');
Route::get('/holidays/petitions', [AdminHolidaysController::class, 'usersPetitions'])->name('holiday.admin.petitions');
Route::get('/holidays/record', [AdminHolidaysController::class, 'addedRecord'])->name('holiday.admin.record');
Route::get('/holidays/history', [AdminHolidaysController::class, 'allHistory'])->name('holiday.admin.history');
Route::get('/holidays/managePetition', [AdminHolidaysController::class, 'managePetition'])->name('holiday.admin.managePetition');
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

// Petition (PETICIONES)
Route::get('/petition', [PetitionController::class, 'index'])->name('peticion.index');
Route::get('/petition/create', [PetitionController::class, 'create'])->name('peticion.create');
Route::get('/petition/edit/{id}', [PetitionController::class, 'edit'])->name('peticion.edit');
Route::post('/petition/store', [PetitionController::class, 'store'])->name('peticion.store');
Route::post('/budpetitionget/update/{id}', [PetitionController::class, 'update'])->name('peticion.update');
Route::post('/petition/destroy', [PetitionController::class, 'destroy'])->name('peticion.delete');


// Budgets (PRESUPUESTOS)
Route::get('/budgets', [BudgetController::class, 'index'])->name('presupuestos.index');
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

// Budgets Concepts (CONCEPTOS DE PRESUPUESTOS)
Route::get('/budget-concepts/{budget}/create-type-own', [BudgetConceptsController::class, 'createTypeOwn'])->name('budgetConcepts.createTypeOwn');
Route::post('/budget-concepts/{budget}/store-type-own', [BudgetConceptsController::class, 'storeTypeOwn'])->name('budgetConcepts.storeTypeOwn');
Route::get('/budget-concepts/{budgetConcept}/edit-type-own', [BudgetConceptsController::class, 'editTypeOwn'])->name('budgetConcepts.editTypeOwn');
Route::post('/budget-concepts/{budgetConcept}/update-type-own', [BudgetConceptsController::class, 'updateTypeOwn'])->name('budgetConcepts.updateTypeOwn');
Route::get('/budget-concepts/{budgetConcept}/destroy-type-own', [BudgetConceptsController::class, 'destroyTypeOwn'])->name('budgetConcepts.destroyTypeOwn');

Route::get('/budget-concepts/{budget}/create-type-supplier', [BudgetConceptsController::class, 'createTypeSupplier'])->name('budgetConcepts.createTypeSupplier');
Route::post('/budget-concepts/{budget}/store-type-supplier', [BudgetConceptsController::class, 'storeTypeSupplier'])->name('budgetConcepts.storeTypeSupplier');
Route::get('/budget-concepts/{budgetConcept}/edit-type-supplier', [BudgetConceptsController::class, 'editTypeSupplier'])->name('budgetConcepts.editTypeSupplier');
Route::get('/budget-concepts/{budgetConcept}/update-type-supplier', [BudgetConceptsController::class, 'updateTypeSupplier'])->name('budgetConcepts.updateTypeSupplier');
Route::get('/budget-concepts/{budgetConcept}/destroy-type-supplier', [BudgetConceptsController::class, 'destroyTypeSupplier'])->name('budgetConcepts.destroyTypeSupplier');

Route::get('/budget-concepts/{categoryId}', [BudgetConceptsController::class, 'getServicesByCategory'])->name('budgetConcepts.getServicesByCategory');
Route::post('/budget-concepts/category-service', [BudgetConceptsController::class, 'getInfoByServices'])->name('budgetConcepts.getInfoByServices');
Route::post('/budget-concepts/delete', [BudgetConceptsController::class, 'deleteConceptsType'])->name('budgetConcepts.delete');
Route::post('/budget-concepts/discount-update', [BudgetConceptsController::class, 'discountUpdate'])->name('budgetConcepts.discountUpdate');

// Projects (CAMPAÑAS)
Route::get('/projects', [ProjectController::class, 'index'])->name('campania.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->name('campania.create');
Route::get('/projects/edit/{id}', [ProjectController::class, 'edit'])->name('campania.edit');
Route::get('/projects/{cliente}/create-from-budget', [ProjectController::class, 'createFromBudget'])->name('campania.createFromBudget');
Route::get('/projects/{cliente}/create-from-budget/{petitionid}', [ProjectController::class, 'createFromBudgetAndPetition'])->name('campania.createFromBudgetAndPetition');
Route::post('/projects/store', [ProjectController::class, 'store'])->name('campania.store');
Route::post('/projects/update/{id}', [ProjectController::class, 'update'])->name('campania.update');
Route::post('/projects/destroy', [ProjectController::class, 'destroy'])->name('campania.delete');
Route::post('/projects/store-from-budget', [ProjectController::class, 'storeFromBudget'])->name('campania.storeFromBudget');
Route::post('/projects/update-from-window', [ProjectController::class, 'updateFromWindow'])->name('campania.updateFromWindow');
Route::post('/projects-from-client', [ProjectController::class, 'postProjectsFromClient'])->name('campania.postProjectsFromClient');

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
Route::get('/dominios', [App\Http\Controllers\Dominios\DominiosController::class, 'index'])->name('dominios.index');
Route::get('/dominios/create', [App\Http\Controllers\Dominios\DominiosController::class, 'create'])->name('dominios.create');
Route::get('/dominios/edit/{id}', [App\Http\Controllers\Dominios\DominiosController::class, 'edit'])->name('dominios.edit');
Route::post('/dominios/store', [App\Http\Controllers\Dominios\DominiosController::class, 'store'])->name('dominios.store');
Route::post('/dominios/update/{id}', [App\Http\Controllers\Dominios\DominiosController::class, 'update'])->name('dominios.update');
Route::post('/dominios/destroy', [App\Http\Controllers\Dominios\DominiosController::class, 'destroy'])->name('dominios.delete');

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

// Gastos sin clasificar (TESORERIA)
Route::get('/gastos-sin-clasificar', [TesoreriaController::class, 'indexUnclassifiedExpensese'])->name('gastos-sin-clasificar.index');
Route::get('/gastos-sin-clasificar/edit/{id}', [TesoreriaController::class, 'editUnclassifiedExpensese'])->name('gasto-sin-clasificar.edit');
Route::post('/gastos-sin-clasificar/update/{id}', [TesoreriaController::class, 'updateUnclassifiedExpensese'])->name('gasto-sin-clasificar.update');
Route::post('/gastos-sin-clasificar/destroy', [TesoreriaController::class, 'destroyUnclassifiedExpensese'])->name('gastos-sin-clasificar.delete');

// Configuracion
Route::get('/configuracion', [SettingsController::class, 'index'])->name('configuracion.index');
Route::post('/configuracion/update/{id}', [SettingsController::class, 'update'])->name('configuracion.update');
Route::post('/configuracion/store', [SettingsController::class, 'store'])->name('configuracion.store');
});

Route::get('/ruta-prueba', function () {
    event(new RecargarPagina(50));
    return view('portal.dashboard');
});
