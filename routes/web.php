<?php

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
Route::get('/user/create', [App\Http\Controllers\Users\UserController::class, 'create'])->name('user.create');
Route::get('/user/edit/{id}', [App\Http\Controllers\Users\UserController::class, 'edit'])->name('user.edit');
Route::post('/user/store', [App\Http\Controllers\Users\UserController::class, 'store'])->name('user.store');
Route::post('/user/update/{id}', [App\Http\Controllers\Users\UserController::class, 'update'])->name('user.update');
Route::get('/user/show/{id}', [App\Http\Controllers\Users\UserController::class, 'show'])->name('user.show');
Route::post('/user/destroy', [App\Http\Controllers\Users\UserController::class, 'destroy'])->name('user.delete');
Route::post('/user/avatar/{id}', [App\Http\Controllers\Users\UserController::class, 'avatar'])->name('user.avatar');

// Clients (CLIENTES)
Route::get('/clients', [App\Http\Controllers\Clients\ClientController::class, 'index'])->name('clientes.index');
Route::get('/client/create', [App\Http\Controllers\Clients\ClientController::class, 'create'])->name('cliente.create');
Route::get('/client/edit/{id}', [App\Http\Controllers\Clients\ClientController::class, 'edit'])->name('cliente.edit');
Route::post('/client/store', [App\Http\Controllers\Clients\ClientController::class, 'store'])->name('cliente.store');
Route::post('/client/update/{id}', [App\Http\Controllers\Clients\ClientController::class, 'update'])->name('cliente.update');
Route::get('/client/show/{id}', [App\Http\Controllers\Clients\ClientController::class, 'show'])->name('cliente.show');
Route::post('/client/destroy', [App\Http\Controllers\Clients\ClientController::class, 'destroy'])->name('cliente.delete');
Route::post('/client/logo/{id}', [App\Http\Controllers\Clients\ClientController::class, 'logo'])->name('cliente.logo');
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
