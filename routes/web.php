<?php

use App\Events\RecargarPagina;
use App\Http\Controllers\Alert\AlertController;
use App\Http\Controllers\Autoseo\AutoseoReports;
use App\Http\Controllers\Autoseo\AutoseoReportsGen;
use App\Http\Controllers\Bajas\BajaController;
use App\Http\Controllers\CrmActivities\CrmActivityMeetingController;
use App\Http\Controllers\Plataforma\ExcelUploadController;
use App\Http\Controllers\Suppliers\SuppliersController;
use App\Http\Controllers\Tesoreria\CuadroController;
use App\Http\Controllers\Tesoreria\TesoreriaController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Budgets\BudgetController;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\Companies\CompanyController;
use App\Http\Controllers\ContactEvents\ContactEventController;
use App\Http\Controllers\Contacts\ContactController;
use App\Http\Controllers\Contracts\ContractController;
use App\Http\Controllers\Cotizaciones\CotizacionController;
use App\Http\Controllers\InvoicesEmitidas\InvoiceEmitidaController;
use App\Http\Controllers\InvoicesRecibidas\InvoiceRecibidaController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\News\NewsController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\Proyectos\ProyectoController;
use App\Http\Controllers\Roles\RoleController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\States\StateController;
use App\Http\Controllers\Tags\TagController;
use App\Http\Controllers\Tasks\TaskController;
use App\Http\Controllers\Calendar\CalendarController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Emails\EmailController;
use App\Http\Controllers\Stats\StatsController;
use App\Http\Controllers\Workers\WorkerController;
use App\Http\Controllers\Expenses\ExpenseController;
use App\Http\Controllers\Api\ClientsApiController;
use App\Http\Controllers\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Subcontract\SubcontractController;
use App\Http\Controllers\Facturas\FacturaController;
use App\Http\Controllers\Remesas\RemesaController;
use App\Http\Controllers\Seguros\SeguroController;
use App\Http\Controllers\Alert\AlertCategoriesController;
use App\Http\Controllers\Cuentas\CuentasController;
use App\Http\Controllers\Bancos\BancosController;
use App\Http\Controllers\Categorias\CategoriasController;
use App\Http\Controllers\Gastos\GastosController;
use App\Http\Controllers\Periodos\PeriodosController;
use App\Http\Controllers\Rrhh\RrhhController;
use App\Http\Controllers\Nominas\NominasController;
use App\Http\Controllers\Contratos\ContratosController;
use App\Http\Controllers\Almacen\AlmacenController;
use App\Http\Controllers\Tipos\TiposController;
use App\Http\Controllers\Grupos\GruposController;
use App\Http\Controllers\InformeEmpleados\InformeEmpleadosController;
use App\Http\Controllers\FormacionTrabajadores\FormacionTrabajadoresController;
use App\Http\Controllers\CartaPortes\CartaPortesController;
use App\Http\Controllers\Choferes\ChoferesController;
use App\Http\Controllers\Camiones\CamionesController;
use App\Http\Controllers\Servicios\ServiciosController;
use App\Http\Controllers\Zonas\ZonasController;
use App\Http\Controllers\FirmaDigital\FirmaDigitalController;
use App\Http\Controllers\Dominios\DominiosController;
use App\Http\Controllers\Hosting\HostingController;
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

Route::name('inicio')->get('/', function () {
    return redirect()->route('portal.loginAdminGet');
});

Route::get('/budget/cliente/{budget}', [BudgetController::class, 'getBudget'])->name('presupuestos.cliente');
Route::post('/budget/acceptance', [BudgetController::class, 'setAcceptance'])->name('presupuestos.cliente.accept');

Auth::routes();

//pdf
Route::get('/invoice/pdf/{invoice}', [InvoiceEmitidaController::class, 'pdf'])->name('invoice.pdf');
Route::get('/invoice/pdf-send/{invoice}', [InvoiceEmitidaController::class, 'sendPdf'])->name('invoice.pdf.send');
Route::get('/cotizacion/pdf/{cotizacion}', [CotizacionController::class, 'pdf'])->name('cotizacion.pdf');
Route::get('/cotizacion/pdf-send/{cotizacion}', [CotizacionController::class, 'sendPdf'])->name('cotizacion.pdf.send');
Route::get('/budget/pdf/{budget}', [BudgetController::class, 'pdf'])->name('budget.pdf');
Route::get('/budget/pdf-send/{budget}', [BudgetController::class, 'sendPdf'])->name('budget.pdf.send');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class);
    Route::get('/profile/{user}', [UserController::class, 'profile'])->name('users.profile');
    Route::get('/updatePassword/{user}', [UserController::class, 'showUpdatePassword'])->name('users.showUpdatePassword');
    Route::post('/updatePassword/{user}', [UserController::class, 'updatePassword'])->name('users.updatePassword');
    Route::post('/users/updateProfile/{user}', [UserController::class, 'updateProfile'])->name('users.updateProfile');
    Route::post('/users/updateProfileImage/{user}', [UserController::class, 'updateProfileImage'])->name('users.updateProfileImage');
    Route::post('/users/deleteProfileImage/{user}', [UserController::class, 'deleteProfileImage'])->name('users.deleteProfileImage');

    // Clients
    Route::resource('clients', ClientController::class);
    Route::get('/clients-search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/clients-search-rrhh', [ClientController::class, 'searchRrhh'])->name('clients.searchRrhh');
    Route::get('/clients/{client}/contacts', [ClientController::class, 'contacts'])->name('clients.contacts');
    Route::get('/clients/{client}/contacts/create', [ClientController::class, 'createContact'])->name('clients.contacts.create');
    Route::post('/clients/{client}/contacts/store', [ClientController::class, 'storeContact'])->name('clients.contacts.store');
    Route::get('/clients/{client}/contacts/{contact}/edit', [ClientController::class, 'editContact'])->name('clients.contacts.edit');
    Route::put('/clients/{client}/contacts/{contact}', [ClientController::class, 'updateContact'])->name('clients.contacts.update');
    Route::get('/clients/{client}/contacts/{contact}/delete', [ClientController::class, 'deleteContact'])->name('clients.contacts.delete');

    // Contacts
    Route::resource('contacts', ContactController::class);
    Route::get('/contacts-search', [ContactController::class, 'search'])->name('contacts.search');

    // Contact Events
    Route::resource('contact-events', ContactEventController::class);

    // Companies
    Route::resource('companies', CompanyController::class);
    Route::get('/companies-search', [CompanyController::class, 'search'])->name('companies.search');

    // Leads
    Route::resource('leads', LeadController::class);
    Route::get('/leads-search', [LeadController::class, 'search'])->name('leads.search');
    Route::post('/leads/changeState/{lead}', [LeadController::class, 'changeState'])->name('leads.changeState');
    Route::post('/leads/changeWorker/{lead}', [LeadController::class, 'changeWorker'])->name('leads.changeWorker');
    Route::post('/leads/changePriority/{lead}', [LeadController::class, 'changePriority'])->name('leads.changePriority');

    // News
    Route::resource('news', NewsController::class);

    // Tags
    Route::resource('tags', TagController::class);

    // States
    Route::resource('states', StateController::class);

    // Workers
    Route::resource('workers', WorkerController::class);
    Route::get('/workers-search', [WorkerController::class, 'search'])->name('workers.search');
    Route::get('/workers-search-all', [WorkerController::class, 'searchAll'])->name('workers.searchAll');

    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/changeState/{task}', [TaskController::class, 'changeState'])->name('tasks.changeState');
    Route::post('/tasks/changeWorker/{task}', [TaskController::class, 'changeWorker'])->name('tasks.changeWorker');
    Route::post('/tasks/changePriority/{task}', [TaskController::class, 'changePriority'])->name('tasks.changePriority');
    Route::post('/tasks/addTag/{task}', [TaskController::class, 'addTag'])->name('tasks.addTag');
    Route::get('/tasks/getByUser/{user}', [TaskController::class, 'getByUser'])->name('tasks.getByUser');

    // Projects
    Route::resource('proyectos', ProyectoController::class);
    Route::post('/proyectos/changeState/{proyecto}', [ProyectoController::class, 'changeState'])->name('proyectos.changeState');
    Route::post('/proyectos/changeWorker/{proyecto}', [ProyectoController::class, 'changeWorker'])->name('proyectos.changeWorker');
    Route::post('/proyectos/changePriority/{proyecto}', [ProyectoController::class, 'changePriority'])->name('proyectos.changePriority');
    Route::post('/proyectos/addTag/{proyecto}', [ProyectoController::class, 'addTag'])->name('proyectos.addTag');

    // Contracts
    Route::resource('contracts', ContractController::class);

    // Invoices Emitidas
    Route::resource('invoices', InvoiceEmitidaController::class);
    Route::get('/invoices-search', [InvoiceEmitidaController::class, 'search'])->name('invoices.search');
    Route::get('/invoices-search-client', [InvoiceEmitidaController::class, 'searchByClient'])->name('invoices.searchByClient');
    Route::post('/invoices/sendEmail/{invoice}', [InvoiceEmitidaController::class, 'sendEmail'])->name('invoices.sendEmail');
    Route::get('/invoices/duplicate/{invoice}', [InvoiceEmitidaController::class, 'duplicate'])->name('invoices.duplicate');
    Route::post('/invoices/changeState/{invoice}', [InvoiceEmitidaController::class, 'changeState'])->name('invoices.changeState');
    Route::post('/invoices/massChangeState', [InvoiceEmitidaController::class, 'massChangeState'])->name('invoices.massChangeState');

    // Invoices Recibidas
    Route::resource('invoices-recibidas', InvoiceRecibidaController::class);
    Route::get('/invoices-recibidas-search', [InvoiceRecibidaController::class, 'search'])->name('invoices-recibidas.search');

    // Budgets
    Route::resource('budgets', BudgetController::class);
    Route::get('/budgets-search', [BudgetController::class, 'search'])->name('budgets.search');
    Route::post('/budgets/sendEmail/{budget}', [BudgetController::class, 'sendEmail'])->name('budgets.sendEmail');
    Route::get('/budgets/duplicate/{budget}', [BudgetController::class, 'duplicate'])->name('budgets.duplicate');
    Route::post('/budgets/changeState/{budget}', [BudgetController::class, 'changeState'])->name('budgets.changeState');
    Route::post('/budgets/toInvoice/{budget}', [BudgetController::class, 'toInvoice'])->name('budgets.toInvoice');

    // Cotizaciones
    Route::resource('cotizaciones', CotizacionController::class);
    Route::get('/cotizaciones-search', [CotizacionController::class, 'search'])->name('cotizaciones.search');
    Route::post('/cotizaciones/sendEmail/{cotizacion}', [CotizacionController::class, 'sendEmail'])->name('cotizaciones.sendEmail');
    Route::get('/cotizaciones/duplicate/{cotizacion}', [CotizacionController::class, 'duplicate'])->name('cotizaciones.duplicate');
    Route::post('/cotizaciones/changeState/{cotizacion}', [CotizacionController::class, 'changeState'])->name('cotizaciones.changeState');
    Route::post('/cotizaciones/toInvoice/{cotizacion}', [CotizacionController::class, 'toInvoice'])->name('cotizaciones.toInvoice');
    Route::post('/cotizaciones/toBudget/{cotizacion}', [CotizacionController::class, 'toBudget'])->name('cotizaciones.toBudget');

    // Roles
    Route::resource('roles', RoleController::class);

    // Settings
    Route::resource('settings', SettingsController::class);

    // Emails
    Route::resource('emails', EmailController::class);
    Route::get('/emails-search', [EmailController::class, 'search'])->name('emails.search');
    Route::post('/emails/reply/{email}', [EmailController::class, 'reply'])->name('emails.reply');
    Route::get('/emails/syncEmails/{email}', [EmailController::class, 'syncEmails'])->name('emails.syncEmails');

    // Calendar
    Route::resource('calendar', CalendarController::class);
    Route::get('/calendar-events', [CalendarController::class, 'getEvents'])->name('calendar.events');

    // Stats
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

    // Expenses
    Route::resource('expenses', ExpenseController::class);

    // Payment Methods
    Route::resource('payment-methods', PaymentMethodController::class);

    // Subcontracts
    Route::resource('subcontracts', SubcontractController::class);

    // Facturas
    Route::resource('facturas', FacturaController::class);
    Route::get('/facturas-search', [FacturaController::class, 'search'])->name('facturas.search');
    Route::post('/facturas/sendEmail/{factura}', [FacturaController::class, 'sendEmail'])->name('facturas.sendEmail');
    Route::get('/facturas/duplicate/{factura}', [FacturaController::class, 'duplicate'])->name('facturas.duplicate');
    Route::post('/facturas/changeState/{factura}', [FacturaController::class, 'changeState'])->name('facturas.changeState');
    Route::post('/facturas/massChangeState', [FacturaController::class, 'massChangeState'])->name('facturas.massChangeState');
    Route::get('/facturas/pdf/{factura}', [FacturaController::class, 'pdf'])->name('facturas.pdf');
    Route::get('/facturas/pdf-send/{factura}', [FacturaController::class, 'sendPdf'])->name('facturas.pdf.send');

    // Remesas
    Route::resource('remesas', RemesaController::class);
    Route::get('/remesas-search', [RemesaController::class, 'search'])->name('remesas.search');
    Route::post('/remesas/addInvoice/{remesa}', [RemesaController::class, 'addInvoice'])->name('remesas.addInvoice');
    Route::post('/remesas/removeInvoice/{remesa}', [RemesaController::class, 'removeInvoice'])->name('remesas.removeInvoice');
    Route::get('/remesas/generate/{remesa}', [RemesaController::class, 'generate'])->name('remesas.generate');

    // Seguros
    Route::resource('seguros', SeguroController::class);
    Route::get('/seguros-search', [SeguroController::class, 'search'])->name('seguros.search');

    // Alert Categories
    Route::resource('alert-categories', AlertCategoriesController::class);

    // Alerts
    Route::resource('alerts', AlertController::class);
    Route::get('/alerts-search', [AlertController::class, 'search'])->name('alerts.search');
    Route::post('/alerts/changeState/{alert}', [AlertController::class, 'changeState'])->name('alerts.changeState');

    // Cuentas
    Route::resource('cuentas', CuentasController::class);

    // Bancos
    Route::resource('bancos', BancosController::class);

    // Categorias
    Route::resource('categorias', CategoriasController::class);

    // Gastos
    Route::resource('gastos', GastosController::class);
    Route::get('/gastos-search', [GastosController::class, 'search'])->name('gastos.search');
    Route::post('/gastos/changeState/{gasto}', [GastosController::class, 'changeState'])->name('gastos.changeState');

    // Periodos
    Route::resource('periodos', PeriodosController::class);

    // Tesoreria
    Route::get('/tesoreria', [TesoreriaController::class, 'index'])->name('tesoreria.index');
    Route::get('/tesoreria/cuadro', [CuadroController::class, 'index'])->name('tesoreria.cuadro');

    // Rrhh
    Route::get('/rrhh', [RrhhController::class, 'index'])->name('rrhh.index');
    Route::get('/rrhh/informe', [InformeEmpleadosController::class, 'index'])->name('rrhh.informe');
    Route::get('/rrhh/formacion', [FormacionTrabajadoresController::class, 'index'])->name('rrhh.formacion');
    Route::get('/rrhh/formacion/create', [FormacionTrabajadoresController::class, 'create'])->name('rrhh.formacion.create');
    Route::post('/rrhh/formacion/store', [FormacionTrabajadoresController::class, 'store'])->name('rrhh.formacion.store');
    Route::get('/rrhh/formacion/{formacion}/edit', [FormacionTrabajadoresController::class, 'edit'])->name('rrhh.formacion.edit');
    Route::put('/rrhh/formacion/{formacion}', [FormacionTrabajadoresController::class, 'update'])->name('rrhh.formacion.update');
    Route::delete('/rrhh/formacion/{formacion}', [FormacionTrabajadoresController::class, 'destroy'])->name('rrhh.formacion.destroy');

    // Nominas
    Route::resource('nominas', NominasController::class);
    Route::get('/nominas-search', [NominasController::class, 'search'])->name('nominas.search');

    // Contratos
    Route::resource('contratos', ContratosController::class);
    Route::get('/contratos-search', [ContratosController::class, 'search'])->name('contratos.search');

    // Almacen
    Route::get('/almacen', [AlmacenController::class, 'index'])->name('almacen.index');
    Route::get('/almacen/create', [AlmacenController::class, 'create'])->name('almacen.create');
    Route::post('/almacen/store', [AlmacenController::class, 'store'])->name('almacen.store');
    Route::get('/almacen/{almacen}/edit', [AlmacenController::class, 'edit'])->name('almacen.edit');
    Route::put('/almacen/{almacen}', [AlmacenController::class, 'update'])->name('almacen.update');
    Route::delete('/almacen/{almacen}', [AlmacenController::class, 'destroy'])->name('almacen.destroy');

    // Tipos
    Route::resource('tipos', TiposController::class);

    // Grupos
    Route::resource('grupos', GruposController::class);

    // Carta Portes
    Route::resource('carta-portes', CartaPortesController::class);
    Route::get('/carta-portes-search', [CartaPortesController::class, 'search'])->name('carta-portes.search');
    Route::get('/carta-portes/pdf/{cartaPorte}', [CartaPortesController::class, 'pdf'])->name('carta-portes.pdf');

    // Choferes
    Route::resource('choferes', ChoferesController::class);
    Route::get('/choferes-search', [ChoferesController::class, 'search'])->name('choferes.search');

    // Camiones
    Route::resource('camiones', CamionesController::class);
    Route::get('/camiones-search', [CamionesController::class, 'search'])->name('camiones.search');

    // Servicios
    Route::resource('servicios', ServiciosController::class);
    Route::get('/servicios-search', [ServiciosController::class, 'search'])->name('servicios.search');

    // Zonas
    Route::resource('zonas', ZonasController::class);
    Route::get('/zonas-search', [ZonasController::class, 'search'])->name('zonas.search');

    // Firma Digital
    Route::resource('firma-digital', FirmaDigitalController::class);
    Route::get('/firma-digital-search', [FirmaDigitalController::class, 'search'])->name('firma-digital.search');

    // Dominios
    Route::resource('dominios', DominiosController::class);
    Route::get('/dominios-search', [DominiosController::class, 'search'])->name('dominios.search');
    Route::get('/dominios/check/{dominio}', [DominiosController::class, 'checkDomain'])->name('dominios.check');
    Route::get('/dominios/renovar/{dominio}', [DominiosController::class, 'renovarDominio'])->name('dominios.renovar');

    // Hosting
    Route::resource('hosting', HostingController::class);
    Route::get('/hosting-search', [HostingController::class, 'search'])->name('hosting.search');

    // Autoseo
    Route::get('/autoseo', [AutoseoReports::class, 'index'])->name('autoseo.index');
    Route::get('/autoseo/create', [AutoseoReports::class, 'create'])->name('autoseo.create');
    Route::post('/autoseo/store', [AutoseoReports::class, 'store'])->name('autoseo.store');
    Route::get('/autoseo/{report}', [AutoseoReports::class, 'show'])->name('autoseo.show');
    Route::get('/autoseo/{report}/edit', [AutoseoReports::class, 'edit'])->name('autoseo.edit');
    Route::put('/autoseo/{report}', [AutoseoReports::class, 'update'])->name('autoseo.update');
    Route::delete('/autoseo/{report}', [AutoseoReports::class, 'destroy'])->name('autoseo.destroy');
    Route::get('/autoseo/{report}/generate', [AutoseoReportsGen::class, 'generate'])->name('autoseo.generate');
    Route::get('/autoseo/{report}/download', [AutoseoReportsGen::class, 'download'])->name('autoseo.download');

    // Bajas
    Route::resource('bajas', BajaController::class);
    Route::get('/bajas-search', [BajaController::class, 'search'])->name('bajas.search');

    // CRM Activities
    Route::resource('crm-activities', CrmActivityMeetingController::class);

    // Excel Upload
    Route::get('/excel-upload', [ExcelUploadController::class, 'index'])->name('excel-upload.index');
    Route::post('/excel-upload/store', [ExcelUploadController::class, 'store'])->name('excel-upload.store');

    // Firma Digital
    Route::get('/firma-digital-sign/{firma}', [FirmaDigitalController::class, 'sign'])->name('firma-digital.sign');
    Route::post('/firma-digital-sign/{firma}', [FirmaDigitalController::class, 'signStore'])->name('firma-digital.signStore');

    // Portal
    Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');
    Route::get('/portal/clients', [PortalController::class, 'clients'])->name('portal.clients');
    Route::get('/portal/clients/{client}', [PortalController::class, 'clientShow'])->name('portal.clientShow');
    Route::get('/portal/invoices', [PortalController::class, 'invoices'])->name('portal.invoices');
    Route::get('/portal/invoices/{invoice}', [PortalController::class, 'invoiceShow'])->name('portal.invoiceShow');
    Route::get('/portal/budgets', [PortalController::class, 'budgets'])->name('portal.budgets');
    Route::get('/portal/budgets/{budget}', [PortalController::class, 'budgetShow'])->name('portal.budgetShow');
    Route::get('/portal/tasks', [PortalController::class, 'tasks'])->name('portal.tasks');
    Route::get('/portal/tasks/{task}', [PortalController::class, 'taskShow'])->name('portal.taskShow');
    Route::get('/portal/contracts', [PortalController::class, 'contracts'])->name('portal.contracts');
    Route::get('/portal/contracts/{contract}', [PortalController::class, 'contractShow'])->name('portal.contractShow');
    Route::get('/portal/projects', [PortalController::class, 'projects'])->name('portal.projects');
    Route::get('/portal/projects/{project}', [PortalController::class, 'projectShow'])->name('portal.projectShow');
    Route::get('/portal/news', [PortalController::class, 'news'])->name('portal.news');
    Route::get('/portal/news/{news}', [PortalController::class, 'newsShow'])->name('portal.newsShow');
    Route::get('/portal/cotizaciones', [PortalController::class, 'cotizaciones'])->name('portal.cotizaciones');
    Route::get('/portal/cotizaciones/{cotizacion}', [PortalController::class, 'cotizacionShow'])->name('portal.cotizacionShow');
    Route::get('/portal/alerts', [PortalController::class, 'alerts'])->name('portal.alerts');
    Route::get('/portal/alerts/{alert}', [PortalController::class, 'alertShow'])->name('portal.alertShow');

});

// Portal Auth routes (outside auth middleware)
Route::get('/portal/login', [PortalController::class, 'loginAdminGet'])->name('portal.loginAdminGet');
Route::post('/portal/login', [PortalController::class, 'loginAdminPost'])->name('portal.loginAdminPost');
Route::get('/portal/logout', [PortalController::class, 'logout'])->name('portal.logout');
Route::get('/portal/client/login', [PortalController::class, 'loginClientGet'])->name('portal.loginClientGet');
Route::post('/portal/client/login', [PortalController::class, 'loginClientPost'])->name('portal.loginClientPost');
Route::get('/portal/client/logout', [PortalController::class, 'logoutClient'])->name('portal.logoutClient');
Route::middleware(['auth:portal'])->group(function () {
    Route::get('/portal/client', [PortalController::class, 'clientIndex'])->name('portal.clientIndex');
    Route::get('/portal/client/invoices', [PortalController::class, 'clientInvoices'])->name('portal.clientInvoices');
    Route::get('/portal/client/invoices/{invoice}', [PortalController::class, 'clientInvoiceShow'])->name('portal.clientInvoiceShow');
    Route::get('/portal/client/budgets', [PortalController::class, 'clientBudgets'])->name('portal.clientBudgets');
    Route::get('/portal/client/budgets/{budget}', [PortalController::class, 'clientBudgetShow'])->name('portal.clientBudgetShow');
    Route::get('/portal/client/tasks', [PortalController::class, 'clientTasks'])->name('portal.clientTasks');
    Route::get('/portal/client/tasks/{task}', [PortalController::class, 'clientTaskShow'])->name('portal.clientTaskShow');
    Route::get('/portal/client/contracts', [PortalController::class, 'clientContracts'])->name('portal.clientContracts');
    Route::get('/portal/client/contracts/{contract}', [PortalController::class, 'clientContractShow'])->name('portal.clientContractShow');
    Route::get('/portal/client/projects', [PortalController::class, 'clientProjects'])->name('portal.clientProjects');
    Route::get('/portal/client/projects/{project}', [PortalController::class, 'clientProjectShow'])->name('portal.clientProjectShow');
    Route::get('/portal/client/news', [PortalController::class, 'clientNews'])->name('portal.clientNews');
    Route::get('/portal/client/news/{news}', [PortalController::class, 'clientNewsShow'])->name('portal.clientNewsShow');
    Route::get('/portal/client/cotizaciones', [PortalController::class, 'clientCotizaciones'])->name('portal.clientCotizaciones');
    Route::get('/portal/client/cotizaciones/{cotizacion}', [PortalController::class, 'clientCotizacionShow'])->name('portal.clientCotizacionShow');
    Route::get('/portal/client/alerts', [PortalController::class, 'clientAlerts'])->name('portal.clientAlerts');
    Route::get('/portal/client/alerts/{alert}', [PortalController::class, 'clientAlertShow'])->name('portal.clientAlertShow');
});

Route::middleware(['auth'])->get('/api/telefonos-clientes-dominios', [\App\Http\Controllers\Dominios\DominiosController::class, 'obtenerTelefonosClientesDominios'])->name('api.telefonos.dominios');

