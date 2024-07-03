<?php

use App\Events\RecargarPagina;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

// Users (USUARIOS)
Route::get('/users', [App\Http\Controllers\Users\UserController::class, 'index'])->name('users.index');
Route::get('/user/create', [App\Http\Controllers\Users\UserController::class, 'create'])->name('users.create');
Route::get('/user/edit/{id}', [App\Http\Controllers\Users\UserController::class, 'edit'])->name('users.edit');
Route::post('/user/store', [App\Http\Controllers\Users\UserController::class, 'store'])->name('user.store');
Route::post('/user/update/{id}', [App\Http\Controllers\Users\UserController::class, 'update'])->name('user.update');
Route::get('/user/show/{id}', [App\Http\Controllers\Users\UserController::class, 'show'])->name('users.show');
Route::post('/user/destroy', [App\Http\Controllers\Users\UserController::class, 'destroy'])->name('users.delete');
Route::post('/user/avatar/{id}', [App\Http\Controllers\Users\UserController::class, 'avatar'])->name('users.avatar');

// Clients (CLIENTES)
Route::get('/clients', [App\Http\Controllers\Clients\ClientController::class, 'index'])->name('clientes.index');
Route::get('/client/create', [App\Http\Controllers\Clients\ClientController::class, 'create'])->name('clientes.create');
Route::get('/client/edit/{id}', [App\Http\Controllers\Clients\ClientController::class, 'edit'])->name('clientes.edit');
Route::post('/client/store', [App\Http\Controllers\Clients\ClientController::class, 'store'])->name('clientes.store');
Route::post('/client/update/{id}', [App\Http\Controllers\Clients\ClientController::class, 'update'])->name('clientes.update');
Route::get('/client/show/{id}', [App\Http\Controllers\Clients\ClientController::class, 'show'])->name('clientes.show');
Route::post('/client/destroy', [App\Http\Controllers\Clients\ClientController::class, 'destroy'])->name('clientes.delete');
Route::post('/client/logo/{id}', [App\Http\Controllers\Clients\ClientController::class, 'logo'])->name('clientes.logo');
Route::get('/client/create-from-budget', [App\Http\Controllers\Clients\ClientController::class, 'createFromBudget'])->name('cliente.createFromBudget');
Route::post('/client/store-from-budget', [App\Http\Controllers\Clients\ClientController::class, 'storeFromBudget'])->name('cliente.storeFromBudget');

// Budgets (PRESUPUESTOS)
Route::get('/budgets', [App\Http\Controllers\Budgets\BudgetController::class, 'index'])->name('presupuestos.index');
Route::get('/budget/create', [App\Http\Controllers\Budgets\BudgetController::class, 'create'])->name('presupuesto.create');
Route::get('/budget/edit/{id}', [App\Http\Controllers\Budgets\BudgetController::class, 'edit'])->name('presupuesto.edit');
Route::post('/budget/store', [App\Http\Controllers\Budgets\BudgetController::class, 'store'])->name('presupuesto.store');
Route::post('/budget/update/{id}', [App\Http\Controllers\Budgets\BudgetController::class, 'update'])->name('presupuesto.update');
Route::get('/budget/show/{id}', [App\Http\Controllers\Budgets\BudgetController::class, 'show'])->name('presupuesto.show');
Route::post('/budget/destroy', [App\Http\Controllers\Budgets\BudgetController::class, 'destroy'])->name('presupuesto.delete');
Route::post('/budget/logo/{id}', [App\Http\Controllers\Budgets\BudgetController::class, 'logo'])->name('presupuesto.logo');
Route::get('/budget/create-from-project/{cliente}', [App\Http\Controllers\Budgets\BudgetController::class, 'createFromProject'])->name('presupuesto.createFromProject');
Route::post('/budget/accept-budget/', [App\Http\Controllers\Budgets\BudgetController::class, 'aceptarPresupuesto'])->name('presupuesto.aceptarPresupuesto');

// Budgets Concepts (CONCEPTOS DE PRESUPUESTOS)
Route::get('/budget-concepts/{budget}/create-type-own', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'createTypeOwn'])->name('budgetConcepts.createTypeOwn');
Route::post('/budget-concepts/{budget}/store-type-own', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'storeTypeOwn'])->name('budgetConcepts.storeTypeOwn');
Route::get('/budget-concepts/{budgetConcept}/edit-type-own', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'editTypeOwn'])->name('budgetConcepts.editTypeOwn');
Route::post('/budget-concepts/{budgetConcept}/update-type-own', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'updateTypeOwn'])->name('budgetConcepts.updateTypeOwn');
Route::get('/budget-concepts/{budgetConcept}/destroy-type-own', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'destroyTypeOwn'])->name('budgetConcepts.destroyTypeOwn');

Route::get('/budget-concepts/{budget}/create-type-supplier', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'createTypeSupplier'])->name('budgetConcepts.createTypeSupplier');
Route::post('/budget-concepts/{budget}/store-type-supplier', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'storeTypeSupplier'])->name('budgetConcepts.storeTypeSupplier');
Route::get('/budget-concepts/{budgetConcept}/edit-type-supplier', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'editTypeSupplier'])->name('budgetConcepts.editTypeSupplier');
Route::get('/budget-concepts/{budgetConcept}/update-type-supplier', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'updateTypeSupplier'])->name('budgetConcepts.updateTypeSupplier');
Route::get('/budget-concepts/{budgetConcept}/destroy-type-supplier', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'destroyTypeSupplier'])->name('budgetConcepts.destroyTypeSupplier');

Route::get('/budget-concepts/{categoryId}', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'getServicesByCategory'])->name('budgetConcepts.getServicesByCategory');
Route::post('/budget-concepts/category-service', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'getInfoByServices'])->name('budgetConcepts.getInfoByServices');
Route::post('/budget-concepts/delete', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'deleteConceptsType'])->name('budgetConcepts.delete');
Route::post('/budget-concepts/discount-update', [App\Http\Controllers\Budgets\BudgetConceptsController::class, 'discountUpdate'])->name('budgetConcepts.discountUpdate');

// Projects (CAMPAÑAS)
Route::get('/projects', [App\Http\Controllers\Projects\ProjectController::class, 'index'])->name('campania.index');
Route::get('/projects/create', [App\Http\Controllers\Projects\ProjectController::class, 'create'])->name('campania.create');
Route::get('/projects/edit/{id}', [App\Http\Controllers\Projects\ProjectController::class, 'edit'])->name('campania.edit');
Route::get('/projects/{cliente}/create-from-budget', [App\Http\Controllers\Projects\ProjectController::class, 'createFromBudget'])->name('campania.createFromBudget');
Route::get('/projects/show/{id}', [App\Http\Controllers\Projects\ProjectController::class, 'show'])->name('campania.show');
Route::post('/projects/store', [App\Http\Controllers\Projects\ProjectController::class, 'store'])->name('campania.store');
Route::post('/projects/update/{id}', [App\Http\Controllers\Projects\ProjectController::class, 'update'])->name('campania.update');
Route::post('/projects/destroy', [App\Http\Controllers\Projects\ProjectController::class, 'destroy'])->name('campania.delete');
Route::post('/projects/store-from-budget', [App\Http\Controllers\Projects\ProjectController::class, 'storeFromBudget'])->name('campania.storeFromBudget');
Route::post('/projects/update-from-window', [App\Http\Controllers\Projects\ProjectController::class, 'updateFromWindow'])->name('campania.updateFromWindow');
Route::post('/projects-from-client', [App\Http\Controllers\Projects\ProjectController::class, 'postProjectsFromClient'])->name('campania.postProjectsFromClient');

// Services (SERVICIOS)
Route::get('/services', [App\Http\Controllers\Services\ServicesController::class, 'index'])->name('servicios.index');
Route::get('/services/create', [App\Http\Controllers\Services\ServicesController::class, 'create'])->name('servicios.create');
Route::get('/services-show/{id}', [App\Http\Controllers\Services\ServicesController::class, 'show'])->name('servicios.show');
Route::get('/services/edit/{id}', [App\Http\Controllers\Services\ServicesController::class, 'edit'])->name('servicios.edit');
Route::post('/services/store', [App\Http\Controllers\Services\ServicesController::class, 'store'])->name('servicios.store');
Route::post('/services/update/{id}', [App\Http\Controllers\Services\ServicesController::class, 'update'])->name('servicios.update');
Route::post('/services/destroy', [App\Http\Controllers\Services\ServicesController::class, 'destroy'])->name('servicios.delete');

// Services Categories (CATEGORIA DE SERVICIOS)
Route::get('/services-categories', [App\Http\Controllers\Services\ServicesCategoriesController::class, 'index'])->name('serviciosCategoria.index');
Route::get('/services-categories/create', [App\Http\Controllers\Services\ServicesController::class, 'servicesCategoriesCreate'])->name('servicios.servicesCategoriesCreate');
Route::get('/services-categories/edit/{id}', [App\Http\Controllers\Services\ServicesController::class, 'servicesCategoriesEdit'])->name('servicios.servicesCategoriesEdit');
Route::post('/services-categories/store', [App\Http\Controllers\Services\ServicesController::class, 'servicesCategoriesStore'])->name('servicios.servicesCategoriesStore');
Route::post('/services-categories/update/{id}', [App\Http\Controllers\Services\ServicesController::class, 'servicesCategoriesUpdate'])->name('servicios.servicesCategoriesUpdate');
Route::post('/services-categories/destroy', [App\Http\Controllers\Services\ServicesController::class, 'servicesCategoriesDestroy'])->name('servicios.servicesCategoriesDestroy');

// Suppliers (PROVEEDORES)
Route::get('/suppliers/{supplier}/get-supplier', [App\Http\Controllers\Suppliers\SuppliersController::class, 'getSupplier'])->name('proveedores.getSupplier');


// Invoice (FACTURAS)
Route::get('/invoices', [App\Http\Controllers\Invoice\InvoiceController::class, 'index'])->name('facturas.index');
Route::get('/invoice/create', [App\Http\Controllers\Invoice\InvoiceController::class, 'create'])->name('factura.create');
Route::get('/invoice/edit/{id}', [App\Http\Controllers\Invoice\InvoiceController::class, 'edit'])->name('factura.edit');
Route::post('/invoice/store', [App\Http\Controllers\Invoice\InvoiceController::class, 'store'])->name('factura.store');
Route::post('/invoice/update/{id}', [App\Http\Controllers\Invoice\InvoiceController::class, 'update'])->name('factura.update');
Route::get('/invoice/show/{id}', [App\Http\Controllers\Invoice\InvoiceController::class, 'show'])->name('factura.show');
Route::post('/invoice/destroy', [App\Http\Controllers\Invoice\InvoiceController::class, 'destroy'])->name('factura.delete');
Route::post('/invoice/logo/{id}', [App\Http\Controllers\Invoice\InvoiceController::class, 'logo'])->name('factura.logo');
Route::get('/invoice/create-from-project/{cliente}', [App\Http\Controllers\Invoice\InvoiceController::class, 'createFromProject'])->name('factura.createFromProject');
Route::post('/invoice/accept-invoice/', [App\Http\Controllers\Invoice\InvoiceController::class, 'aceptarPresupuesto'])->name('factura.aceptarPresupuesto');


// Task (TAREAS)
Route::get('/tasks', [App\Http\Controllers\Tasks\TasksController::class, 'index'])->name('tareas.index');
Route::get('/task/create', [App\Http\Controllers\Budgets\BudgetController::class, 'create'])->name('tarea.create');
Route::get('/task/edit/{id}', [App\Http\Controllers\Budgets\BudgetController::class, 'edit'])->name('tarea.edit');
Route::post('/task/store', [App\Http\Controllers\Budgets\BudgetController::class, 'store'])->name('tarea.store');
Route::post('/task/update/{id}', [App\Http\Controllers\Budgets\BudgetController::class, 'update'])->name('tarea.update');
Route::get('/task/show/{id}', [App\Http\Controllers\Budgets\BudgetController::class, 'show'])->name('tarea.show');
Route::post('/task/destroy', [App\Http\Controllers\Budgets\BudgetController::class, 'destroy'])->name('tarea.delete');
Route::post('/task/logo/{id}', [App\Http\Controllers\Budgets\BudgetController::class, 'logo'])->name('tarea.logo');
Route::get('/task/create-from-project/{cliente}', [App\Http\Controllers\Budgets\BudgetController::class, 'createFromProject'])->name('tarea.createFromProject');
Route::post('/task/accept-task/', [App\Http\Controllers\Budgets\BudgetController::class, 'aceptarPresupuesto'])->name('tarea.aceptarPresupuesto');


// Dominios
Route::get('/dominios', [App\Http\Controllers\Dominios\DominiosController::class, 'index'])->name('dominios.index');
Route::get('/dominios/create', [App\Http\Controllers\Dominios\DominiosController::class, 'create'])->name('dominios.create');
Route::get('/dominios/edit/{id}', [App\Http\Controllers\Dominios\DominiosController::class, 'edit'])->name('dominios.edit');
Route::post('/dominios/store', [App\Http\Controllers\Dominios\DominiosController::class, 'store'])->name('dominios.store');
Route::post('/dominios/update/{id}', [App\Http\Controllers\Dominios\DominiosController::class, 'update'])->name('dominios.update');
Route::post('/dominios/destroy', [App\Http\Controllers\Dominios\DominiosController::class, 'destroy'])->name('dominios.delete');

// web.php
Route::post('/save-theme-preference', [App\Http\Controllers\Users\UserController::class, 'saveThemePreference'])->name('saveThemePreference');

// Portal Clientes
Route::get('/portal/dashboard', [App\Http\Controllers\Portal\PortalClientesController::class, 'dashboard'])->name('portal.dashboard');
Route::get('/portal/presupuestos', [App\Http\Controllers\Portal\PortalClientesController::class, 'presupuestos'])->name('portal.presupuestos');
Route::get('/portal/facturas', [App\Http\Controllers\Portal\PortalClientesController::class, 'facturas'])->name('portal.facturas');

// Ingresos (TESORERIA)
Route::get('/ingresos', [App\Http\Controllers\Tesoreria\TesoreriaController::class, 'indexIngresos'])->name('ingresos.index');
Route::get('/ingreso/create', [App\Http\Controllers\Tesoreria\TesoreriaController::class, 'createIngresos'])->name('ingreso.create');
Route::get('/ingreso/edit/{id}', [App\Http\Controllers\Tesoreria\TesoreriaController::class, 'editIngresos'])->name('ingreso.edit');
Route::get('/ingreso/show/{id}', [App\Http\Controllers\Tesoreria\TesoreriaController::class, 'showIngresos'])->name('ingreso.show');
Route::post('/ingreso/store', [App\Http\Controllers\Tesoreria\TesoreriaController::class, 'storeIngresos'])->name('ingreso.store');
Route::post('/ingreso/update/{id}', [App\Http\Controllers\Tesoreria\TesoreriaController::class, 'updateIngresos'])->name('ingreso.update');
Route::post('/ingreso/destroy', [App\Http\Controllers\Tesoreria\TesoreriaController::class, 'destroyIngresos'])->name('ingreso.delete');

Route::get('/ruta-prueba', function () {
    event(new RecargarPagina(50));
    return view('portal.dashboard');
});