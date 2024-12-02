<?php

use App\Events\RecargarPagina;
use App\Http\Controllers\AccionesController;
use App\Http\Controllers\Alert\AlertController;
use App\Http\Controllers\Bajas\BajaController;
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
use App\Http\Controllers\Contabilidad\PlanContableController;
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
use App\Http\Controllers\Email\CategoryEmailController;
use App\Http\Controllers\Email\EmailController;
use App\Http\Controllers\Email\StatusMailController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\GrupoContabilidadController;
use App\Http\Controllers\Holiday\HolidayController;
use App\Http\Controllers\Holiday\AdminHolidaysController;
use App\Http\Controllers\Horas\HorasController;
use App\Http\Controllers\Incidence\IncidenceController;
use App\Http\Controllers\KitDigitalController;
use App\Http\Controllers\Logs\LogActionsController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\Nominas\NominasController;
use App\Http\Controllers\Ordenes\OrdenesController;
use App\Http\Controllers\Portal\PortalClientesController;
use App\Http\Controllers\Productividad\ProductividadController;
use App\Http\Controllers\Settings\UserSettingsController;
use App\Http\Controllers\Statistics\StatisticsController;
use App\Http\Controllers\Tesoreria\CategoriaAsociadosController;
use App\Http\Controllers\Tesoreria\CategoriaGastosController;
use App\Http\Controllers\Tesoreria\IvaController;
use App\Http\Controllers\Users\DepartamentController;
use App\Http\Controllers\Users\PositionController;
use App\Http\Controllers\Whatsapp\WhatsappController;

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

Route::get('/budget/cliente/{budget}', [BudgetController::class, 'getBudget'])->name('presupuestos.cliente');
Route::post('/budget/acceptance', [BudgetController::class, 'setAcceptance'])->name('presupuestos.cliente.accept');

Auth::routes();

//pdf
Route::post('/invoice/generate-pdf', [InvoiceController::class, 'generatePDF'])->name('factura.generarPDF');
Route::post('/budget/generate-pdf', [BudgetController::class, 'generatePDF'])->name('presupuesto.generarPDF');



Route::group(['middleware' => 'auth'], function () {

    Route::middleware(['access.level:4'])->group(function () {

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

        //Order
        Route::get('/order', [OrdenesController::class, 'index'])->name('order.index');
        Route::get('/orderAll', [OrdenesController::class, 'indexAll'])->name('order.indexAll');

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
        Route::post('/budgets-by-client', [BudgetController::class, 'getBudgetsByClientId']);
        Route::post('/budgets-by-project', [BudgetController::class, 'getBudgetsByprojectId']);
        Route::post('/budget-by-id', [BudgetController::class, 'getBudgetById']);
        Route::get('/status-projects', [BudgetController::class, 'statusProjects'])->name('presupuestos.status');
        Route::post('/budget/send/{budget}', [BudgetController::class, 'sendEmail'])->name('presupuestos.sendEmail');

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
        Route::get('/projects/show/{id}', [ProjectController::class, 'show'])->name('campania.show');
        Route::get('/projects/edit/{id}', [ProjectController::class, 'edit'])->name('campania.edit');
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
        Route::post('/invoice/electronica', [InvoiceController::class, 'electronica'])->name('factura.electronica');
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('facturas.index');
        Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('factura.create');
        Route::get('/invoice/edit/{id}', [InvoiceController::class, 'edit'])->name('factura.edit');
        Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('factura.store');
        Route::post('/invoice/update/{id}', [InvoiceController::class, 'update'])->name('factura.update');
        Route::get('/invoice/show/{id}', [InvoiceController::class, 'show'])->name('factura.show');
        Route::post('/invoice/destroy', [InvoiceController::class, 'destroy'])->name('factura.delete');
        Route::post('/invoice/paid-invoice', [InvoiceController::class, 'cobrarFactura'])->name('factura.cobrada');
        Route::post('/invoice/rectify', [InvoiceController::class, 'rectificateInvoice'])->name('factura.rectificada');
        Route::get('/invoice/generateMultiplePDFs', [InvoiceController::class, 'generateMultiplePDFs'])->name('factura.generateMultiplePDFs');
        Route::post('/invoice/sendInvoicePDF', [InvoiceController::class, 'sendInvoicePDF'])->name('factura.sendInvoicePDF');

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
        Route::get('/task/calendar/{id}', [TasksController::class, 'calendar'])->name('tarea.calendar');


        // Dominios
        Route::get('/dominios', [DominiosController::class, 'index'])->name('dominios.index');
        Route::get('/dominios/create', [DominiosController::class, 'create'])->name('dominios.create');
        Route::get('/dominios/edit/{id}', [DominiosController::class, 'edit'])->name('dominios.edit');
        Route::post('/dominios/store', [DominiosController::class, 'store'])->name('dominios.store');
        Route::post('/dominios/update/{id}', [DominiosController::class, 'update'])->name('dominios.update');
        Route::post('/dominios/destroy', [DominiosController::class, 'destroy'])->name('dominios.delete');

        // Kit Digital
        Route::get('/kit-digital-whatsapp', [KitDigitalController::class, 'index'])->name('kitDigital.indexWhatsapp');
        Route::get('/kit-digital', [KitDigitalController::class, 'listarClientes'])->name('kitDigital.index');
        Route::get('/kit-digital/create', [KitDigitalController::class, 'create'])->name('kitDigital.create');
        Route::post('/kit-digital/store', [KitDigitalController::class, 'store'])->name('kitDigital.store');
        Route::post('/kit-digital/updatedata', [KitDigitalController::class, 'updateData'])->name('kitDigital.updateData');
        Route::get('/kit-digital/whatsapp/{id}', [KitDigitalController::class, 'whatsapp'])->name('kitDigital.whatsapp');

         // Gastos asociados (TESORERIA)
         Route::get('/gastos-asociados', [TesoreriaController::class, 'indexAssociatedExpenses'])->name('gasto-asociados.index');
         Route::get('/gasto-asociado/create', [TesoreriaController::class, 'createAssociatedExpenses'])->name('gasto-asociado.create');
         Route::get('/gasto-asociado/edit/{id}', [TesoreriaController::class, 'editAssociatedExpenses'])->name('gasto-asociado.edit');
         Route::post('/gasto-asociado/store', [TesoreriaController::class, 'storeAssociatedExpenses'])->name('gasto-asociado.store');
         Route::post('/gasto-asociado/update/{id}', [TesoreriaController::class, 'updateAssociatedExpenses'])->name('gasto-asociado.update');
         Route::post('/gasto-asociado/destroy', [TesoreriaController::class, 'destroyAssociatedExpenses'])->name('gasto-asociado.delete');
    });

    Route::middleware(['access.level:3'])->group(function () {

        //Holidays(Vacaciones Admin)
        Route::get('/holiday/index', [AdminHolidaysController::class, 'index'])->name('holiday.admin.index');
        Route::get('/holiday/admin-create', [AdminHolidaysController::class, 'create'])->name('holiday.admin.create');
        Route::get('/holiday/store', [AdminHolidaysController::class, 'store'])->name('holiday.admin.store');
        Route::get('/holiday/destroy', [AdminHolidaysController::class, 'destroy'])->name('holiday.admin.destroy');
        Route::get('/holidays/admin-edit/{id}', [AdminHolidaysController::class, 'edit'])->name('holiday.admin.edit');
        Route::post('/holidays/admin-update', [AdminHolidaysController::class, 'update'])->name('holiday.admin.update');
        Route::get('/holidays/petitions', [AdminHolidaysController::class, 'usersPetitions'])->name('holiday.admin.petitions');
        Route::get('/holidays/record', [AdminHolidaysController::class, 'addedRecord'])->name('holiday.admin.record');
        Route::get('/holidays/history', [AdminHolidaysController::class, 'allHistory'])->name('holiday.admin.history');
        Route::get('/holidays/managePetition/{id}', [AdminHolidaysController::class, 'managePetition'])->name('holiday.admin.managePetition');
        Route::post('/holidays/acceptHolidays', [AdminHolidaysController::class, 'acceptHolidays'])->name('holiday.admin.acceptHolidays');
        Route::post('/holidays/denyHolidays', [AdminHolidaysController::class, 'denyHolidays'])->name('holiday.admin.denyHolidays');
        Route::post('/holidays/getDate/{holidaysPetitions}', [AdminHolidaysController::class, 'getDate'])->name('holiday.admin.getDate');

        // Users (USUARIOS)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/user/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::post('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::get('/user/show/{id}', [UserController::class, 'show'])->name('users.show');
        Route::post('/user/destroy', [UserController::class, 'destroy'])->name('users.delete');
        Route::post('/user/avatar/{id}', [UserController::class, 'avatar'])->name('users.avatar');

        //Bajas
        Route::get('/bajas', [BajaController::class, 'index'])->name('bajas.index');
        Route::get('/baja/create', [BajaController::class, 'create'])->name('bajas.create');
        Route::get('/baja/edit/{baja}', [BajaController::class, 'edit'])->name('bajas.edit');
        Route::post('/bajas/store', [BajaController::class, 'store'])->name('bajas.store');
        Route::post('/bajas/update/{baja}', [BajaController::class, 'update'])->name('bajas.update');
        Route::post('/bajas/delete', [BajaController::class, 'destroy'])->name('bajas.delete');

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


        // Ingresos (TESORERIA)
        Route::get('/ingresos', [TesoreriaController::class, 'indexIngresos'])->name('ingreso.index');
        Route::get('/ingreso/create', [TesoreriaController::class, 'createIngresos'])->name('ingreso.create');
        Route::get('/ingreso/edit/{id}', [TesoreriaController::class, 'editIngresos'])->name('ingreso.edit');
        Route::get('/ingreso/show/{id}', [TesoreriaController::class, 'showIngresos'])->name('ingreso.show');
        Route::post('/ingreso/store', [TesoreriaController::class, 'storeIngresos'])->name('ingreso.store');
        Route::post('/ingreso/update/{id}', [TesoreriaController::class, 'updateIngresos'])->name('ingreso.update');
        Route::post('/ingreso/destroy', [TesoreriaController::class, 'destroyIngresos'])->name('ingreso.delete');

        // Gastos (TESORERIA)
        Route::get('/gastos', [TesoreriaController::class, 'indexGastos'])->name('gasto.index');
        Route::get('/gasto/create', [TesoreriaController::class, 'createGastos'])->name('gasto.create');
        Route::get('/gasto/edit/{id}', [TesoreriaController::class, 'editGastos'])->name('gasto.edit');
        Route::get('/gasto/show/{id}', [TesoreriaController::class, 'showGastos'])->name('gasto.show');
        Route::post('/gasto/store', [TesoreriaController::class, 'storeGastos'])->name('gasto.store');
        Route::post('/gasto/update/{id}', [TesoreriaController::class, 'updateGastos'])->name('gasto.update');
        Route::post('/gasto/destroy', [TesoreriaController::class, 'destroyGastos'])->name('gasto.delete');

        //Traspasos
        Route::get('/traspasos', [TesoreriaController::class, 'indexTraspasos'])->name('traspasos.index');
        Route::get('/traspasos/create', [TesoreriaController::class, 'createTraspasos'])->name('traspasos.create');
        Route::get('/traspasos/edit/{id}', [TesoreriaController::class, 'editTraspasos'])->name('traspasos.edit');
        Route::post('/traspasos/store', [TesoreriaController::class, 'storeTraspasos'])->name('traspasos.store');
        Route::post('/traspasos/update/{id}', [TesoreriaController::class, 'updateTraspasos'])->name('traspasos.update');
        Route::post('/traspasos/destroy', [TesoreriaController::class, 'destroyTraspasos'])->name('traspasos.delete');


        // Gastos sin clasificar (TESORERIA)
        Route::get('/gastos-sin-clasificar', [TesoreriaController::class, 'indexUnclassifiedExpensese'])->name('gasto-sin-clasificar.index');
        Route::get('/gastos-sin-clasificar/edit/{id}', [TesoreriaController::class, 'editUnclassifiedExpensese'])->name('gasto-sin-clasificar.edit');
        Route::post('/gastos-sin-clasificar/update/{id}', [TesoreriaController::class, 'updateUnclassifiedExpensese'])->name('gasto-sin-clasificar.update');
        Route::post('/gastos-sin-clasificar/destroy', [TesoreriaController::class, 'destroyUnclassifiedExpensese'])->name('gastos-sin-clasificar.delete');

        //Categorias de gastos asociados
        Route::get('/categorias-gastos-asociados', [CategoriaAsociadosController::class, 'index'])->name('categorias-gastos-asociados.index');
        Route::get('/categorias-gastos-asociados/create', [CategoriaAsociadosController::class, 'create'])->name('categorias-gastos-asociados.create');
        Route::post('/categorias-gastos-asociados/store', [CategoriaAsociadosController::class, 'store'])->name('categorias-gastos-asociados.store');
        Route::get('/categorias-gastos-asociados/edit/{id}', [CategoriaAsociadosController::class, 'edit'])->name('categorias-gastos-asociados.edit');
        Route::post('/categorias-gastos-asociados/update/{id}', [CategoriaAsociadosController::class, 'update'])->name('categorias-gastos-asociados.update');
        Route::post('/categorias-gastos-asociados/destroy', [CategoriaAsociadosController::class, 'destroy'])->name('categorias-gastos-asociados.delete');

        //Categorias de gastos
        Route::get('/categorias-gastos', [CategoriaGastosController::class, 'index'])->name('categorias-gastos.index');
        Route::get('/categorias-gastos/create', [CategoriaGastosController::class, 'create'])->name('categorias-gastos.create');
        Route::post('/categorias-gastos/store', [CategoriaGastosController::class, 'store'])->name('categorias-gastos.store');
        Route::get('/categorias-gastos/edit/{id}', [CategoriaGastosController::class, 'edit'])->name('categorias-gastos.edit');
        Route::post('/categorias-gastos/update/{id}', [CategoriaGastosController::class, 'update'])->name('categorias-gastos.update');
        Route::post('/categorias-gastos/destroy', [CategoriaGastosController::class, 'destroy'])->name('categorias-gastos.delete');

        //Ivas
        Route::get('/ivas', [IvaController::class, 'index'])->name('iva.index');
        Route::get('/iva/create', [IvaController::class, 'create'])->name('iva.create');
        Route::post('/iva/store', [IvaController::class, 'store'])->name('iva.store');
        Route::get('/iva/edit/{id}', [IvaController::class, 'edit'])->name('iva.edit');
        Route::post('/iva/update/{id}', [IvaController::class, 'update'])->name('iva.update');
        Route::post('/iva/destroy', [IvaController::class, 'destroy'])->name('iva.delete');

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

        // Categoria de Emails
        Route::get('/category-email', [CategoryEmailController::class, 'index'])->name('admin.categoriaEmail.index');
        Route::get('/category-email/create', [CategoryEmailController::class, 'create'])->name('admin.categoriaEmail.create');
        Route::post('/category-email/store', [CategoryEmailController::class, 'store'])->name('admin.categoriaEmail.store');
        Route::get('/category-email/{id}/edit', [CategoryEmailController::class, 'edit'])->name('admin.categoriaEmail.edit');
        Route::put('/category-email/{id}/update', [CategoryEmailController::class, 'update'])->name('admin.categoriaEmail.update');
        Route::post('/category-email/{id}/destroy', [CategoryEmailController::class, 'destroy'])->name('admin.categoriaEmail.destroy');

        // Estados de Emails
        Route::get('/status-mail', [StatusMailController::class, 'index'])->name('admin.statusMail.index');
        Route::get('/status-mail/create', [StatusMailController::class, 'create'])->name('admin.statusMail.create');
        Route::post('/status-mail/store', [StatusMailController::class, 'store'])->name('admin.statusMail.store');
        Route::get('/status-mail/{id}/edit', [StatusMailController::class, 'edit'])->name('admin.statusMail.edit');
        Route::put('/status-mail/{id}/update', [StatusMailController::class, 'update'])->name('admin.statusMail.update');
        Route::post('/status-mail/{id}/destroy', [StatusMailController::class, 'destroy'])->name('admin.statusMail.destroy');


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


        Route::get('/plan-contable', [PlanContableController::class, 'index'])->name('admin.planContable.index');
        Route::get('/plan-contable/json', [PlanContableController::class, 'json']);

        //Logs
        Route::get('/logs',[LogActionsController::class, 'index'])->name('logs.index');
        Route::get('/logs/clasificado',[LogActionsController::class, 'Clasificacion'])->name('logs.clasificado');

        //Productividad
        Route::get('/productividad', [ProductividadController::class, 'index'])->name('productividad.index');
    });

    Route::post('/get-produccion', [DashboardController::class, 'getProduccion'])->name('productividad.get');
    Route::post('/get-gestion', [DashboardController::class, 'getGestion'])->name('gestion.get');
    Route::post('/get-comercial', [DashboardController::class, 'getComercial'])->name('comercial.get');
    Route::post('/get-contabilidad', [DashboardController::class, 'getContabilidad'])->name('contabilidad.get');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //Alertas
    Route::post('/user/alerts', [AlertController::class, 'getUserAlerts'])->name('user.alerts');
    Route::post('/alert/update', [AlertController::class, 'updateStatusAlert'])->name('alert.update');
    Route::post('/alert/postpone', [AlertController::class, 'postpone'])->name('alert.postpone');


    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/getDataTask', [DashboardController::class, 'getDataTask'])->name('dashboard.getDataTask');
    Route::post('/dashboard/getTasksRefresh', [DashboardController::class, 'getTasksRefresh'])->name('dashboard.getTasksRefresh');
    Route::post('/dashboard/setStatusTask', [DashboardController::class, 'setStatusTask'])->name('dashboard.setStatusTask');
    Route::post('/dashboard/llamada', [DashboardController::class, 'llamada'])->name('llamada.store');
    Route::post('/dashboard/llamadafin', [DashboardController::class, 'finalizar'])->name('llamada.end');
    Route::post('/dashboard/timeworked', [DashboardController::class, 'timeworked'])->name('user.time');
    Route::post('/dashboard/updateStatusAlertAndAcceptHours', [DashboardController::class, 'updateStatusAlertAndAcceptHours'])->name('user.updateStatusAlertAndAcceptHours');
    Route::post('/dashboard/responseAlert', [DashboardController::class, 'responseAlert'])->name('user.responseAlert');

    Route::post('/start-jornada', [DashboardController::class, 'startJornada'])->name('dashboard.startJornada');
    Route::post('/end-jornada', [DashboardController::class, 'endJornada'])->name('dashboard.endJornada');
    Route::post('/start-pause', [DashboardController::class, 'startPause'])->name('dashboard.startPause');
    Route::post('/end-pause', [DashboardController::class, 'endPause'])->name('dashboard.endPause');

    //Jornadas
    Route::get('/jornadas', [HorasController::class, 'indexHoras'])->name('horas.index');
    Route::get('/exportarjornadas', [HorasController::class, 'exportHoras'])->name('horas.export');

    //Events(Eventos del to-do)
    Route::post('/event/store', [EventController::class, 'store'])->name('event.store');
    Route::post('/todos/store', [To_doController::class, 'store'])->name('todos.store');
    Route::post('/todos/finish/{id}', [To_doController::class, 'finish'])->name('todos.finalizar');
    Route::post('/todos/complete/{id}', [To_doController::class, 'complete'])->name('todos.completar');
    Route::post('/todos/unread-messages-count/{todoId}', [To_doController::class, 'getUnreadMessagesCount']);
    Route::post('/message/store', [MessageController::class, 'store'])->name('message.store');
    Route::post('/mark-as-read/{todoId}', [MessageController::class,'markAsRead']);
    Route::post('/todos/getMessages/{todoId}', [MessageController::class, 'getMessages']);


    //Meetings(Reuniones)
    Route::get('/meeting', [CrmActivityMeetingController::class, 'index'])->name('reunion.index');
    Route::get('/meeting/create', [CrmActivityMeetingController::class, 'createMeetingFromAllUsers'])->name('reunion.create');
    Route::get('/view-meeting/{id}', [CrmActivityMeetingController::class, 'viewMeeting'])->name('reunion.view');
    Route::post('/meeting/store', [CrmActivityMeetingController::class, 'storeMeetingFromAllUsers'])->name('reunion.store');
    Route::post('/meeting/alreadyRead/{id}', [CrmActivityMeetingController::class, 'alreadyRead'])->name('reunion.alreadyRead');
    Route::post('/meeting/addComments/{id}', [CrmActivityMeetingController::class, 'addCommentsToMeeting'])->name('reunion.addComments');

    // Actas de Reunion
    Route::post('/transcribir-acta', [CrmActivityMeetingController::class,'transcripcion'])->name('admin.trascricion.update');
    Route::post('/enviar-acta', [CrmActivityMeetingController::class,'sendMeetingEmails'])->name('admin.acta.sendEmails');
    Route::post('/registrar-acta', [CrmActivityMeetingController::class,'register'])->name('admin.acta.register');

    //Audios
    Route::post('/store-audio', [CrmActivityMeetingController::class,'storeAudio'])->name('audio.store');

    //Holidays(Vacaciones users)
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holiday.index');
    Route::get('/holidays/edit/{id}', [HolidayController::class, 'edit'])->name('holiday.edit');
    Route::post('/holidays/store', [HolidayController::class, 'store'])->name('holiday.store');
    Route::get('/holidays/create', [HolidayController::class, 'create'])->name('holiday.create');


    //Nominas
    Route::get('/nominas', [NominasController::class, 'index'])->name('nominas.index');
    Route::get('/nominas/user/{id}', [NominasController::class, 'indexUser'])->name('nominas.index_user');
    Route::get('/nominas/create', [NominasController::class, 'create'])->name('nominas.create');
    Route::get('/nominas/edit/{id}', [NominasController::class, 'edit'])->name('nominas.edit');
    Route::get('/nominas/show/{id}', [NominasController::class, 'show'])->name('nominas.show');
    Route::post('/nominas/store', [NominasController::class, 'store'])->name('nominas.store');
    Route::post('/nominas/update/{id}', [NominasController::class, 'update'])->name('nominas.update');
    Route::post('/nominas/destroy', [NominasController::class, 'destroy'])->name('nominas.delete');


    //Contratos
    Route::get('/contratos', [ContratosController::class, 'index'])->name('contratos.index');
    Route::get('/contratos/user/{id}', [ContratosController::class, 'indexUser'])->name('contratos.index_user');
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

    // Incidencias
    Route::get('/incidencias', [IncidenceController::class, 'index'])->name('incidencias.index');
    Route::get('/incidencias/create', [IncidenceController::class, 'create'])->name('incidencias.create');
    Route::get('/incidencias/edit/{id}', [IncidenceController::class, 'edit'])->name('incidencias.edit');
    Route::post('/incidencias/store', [IncidenceController::class, 'store'])->name('incidencias.store');
    Route::post('/incidencias/update/{id}', [IncidenceController::class, 'update'])->name('incidencias.update');
    Route::post('/incidencias/destroy', [IncidenceController::class, 'destroy'])->name('incidencias.delete');

    // web.php
    Route::post('/save-theme-preference', [UserController::class, 'saveThemePreference'])->name('saveThemePreference');

    //kit digital comercial
    Route::post('/kit-digital/storeComercial', [KitDigitalController::class, 'storeComercial'])->name('kitDigital.storeComercial');

    // Emails
    Route::get('/emails', [EmailController::class, 'index'])->name('admin.emails.index');
    Route::get('/emails/create', [EmailController::class, 'create'])->name('admin.emails.create');
    Route::get('/email/{email}/show', [EmailController::class, 'show'])->name('admin.emails.show');
    Route::post('/emails/send', [EmailController::class, 'sendEmail'])->name('admin.emails.send');
    Route::get('/emails/relpy/{emailId}', [EmailController::class, 'reply'])->name('admin.emails.reply');
    Route::post('/emails/sendReply/{emailId}', [EmailController::class, 'replyToEmail'])->name('admin.emails.sendReply');
    Route::get('/emails/settings', [UserSettingsController::class, 'emailSettings'])->name('admin.emailConfig.settings');
    Route::post('/emails/settings/store', [UserSettingsController::class, 'store'])->name('admin.emailConfig.store');
    Route::put('/emails/settings/update/{id}', [UserSettingsController::class, 'update'])->name('admin.emailConfig.update');
    Route::post('/emails/unread', [EmailController::class, 'countUnread'])->name('admin.emails.unread');
    Route::post('/emails/delete', [EmailController::class, 'destroy'])->name('admin.emails.destroy');



    Route::post('/save-order', [BudgetController::class, 'saveOrder'])->name('save.order');


    //Whatsapp
    Route::get('/whatsapp', [WhatsappController::class, 'hookWhatsapp'])->name('whatsapp.hookWhatsapp');
    Route::post('/whatsapp', [WhatsappController::class, 'processHookWhatsapp'])->name('whatsapp.processHookWhatsapp');
    Route::get('/chatgpt/{texto}', [WhatsappController::class, 'chatGptPruebas'])->name('whatsapp.chatGptPruebas');

    Route::get('/mensajes-whatsapp', [WhatsappController::class, 'whatsapp'])->name('whatsapp.mensajes');
    Route::get('/acciones', [AccionesController::class, 'index'])->name('acciones.index');
    Route::get('/acciones/enviar', [AccionesController::class, 'enviar'])->name('acciones.enviar');
    Route::post('/acciones/enviar-mensajes', [AccionesController::class, 'enviarMensajes'])->name('acciones.enviarMensajes');
    Route::post('/listar-mensajes/{id}', [AccionesController::class, 'listarMensajes'])->name('acciones.listarMensajes');
    Route::post('/acciones/enviar-segmento-3', [AccionesController::class, 'enviarMensajesSegmentos'])->name('acciones.enviarMensajesSegmentos');

    Route::post('/actualizar', [AccionesController::class, 'actualizar'])->name('acciones.actualizar');



});
// Portal Clientes
Route::get('/portal', [PortalClientesController::class, 'login'])->name('portal.login');
Route::post('/portal/login', [PortalClientesController::class, 'loginPost'])->name('portal.loginPost');
Route::get('/portal/dashboard', [PortalClientesController::class, 'dashboard'])->name('portal.dashboard');
Route::get('/portal/presupuestos', [PortalClientesController::class, 'presupuestos'])->name('portal.presupuestos');
Route::get('/portal/facturas', [PortalClientesController::class, 'facturas'])->name('portal.facturas');
Route::get('/portal/taskview', [PortalClientesController::class, 'pageTasksViewer'])->name('portal.taskview');
Route::get('/portal/changePin', [PortalClientesController::class, 'changePin'])->name('portal.changePin');
Route::post('/portal/setPin', [PortalClientesController::class, 'setPin'])->name('portal.setPin');
Route::get('/portal/presupuesto/{id}', [PortalClientesController::class, 'showBudget'])->name('portal.showBudget');
Route::get('/portal/factura/{id}', [PortalClientesController::class, 'showInvoice'])->name('portal.showInvoice');

Route::get('/actualizar', [OrdenesController::class, 'actualizar'])->name('actualizar');

