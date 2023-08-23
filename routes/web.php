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

// Users
Route::get('/users', [App\Http\Controllers\Users\UserController::class, 'index'])->name('users.index');
Route::get('/user/create', [App\Http\Controllers\Users\UserController::class, 'create'])->name('user.create');
Route::get('/user/edit/{id}', [App\Http\Controllers\Users\UserController::class, 'edit'])->name('user.edit');
Route::post('/user/store', [App\Http\Controllers\Users\UserController::class, 'store'])->name('user.store');
Route::post('/user/update/{id}', [App\Http\Controllers\Users\UserController::class, 'update'])->name('user.update');
Route::get('/user/show/{id}', [App\Http\Controllers\Users\UserController::class, 'show'])->name('user.show');
Route::post('/user/destroy', [App\Http\Controllers\Users\UserController::class, 'destroy'])->name('user.delete');
Route::post('/user/avatar/{id}', [App\Http\Controllers\Users\UserController::class, 'avatar'])->name('user.avatar');
