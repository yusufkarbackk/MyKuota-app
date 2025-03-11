<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TemplateController;
use App\Services\AccountServices;
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

Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
Route::get('/accounts/create', [AccountController::class, 'create']);
Route::get('/accounts/createCSV', [AccountController::class, 'createCSV']);
Route::post('/accounts/storeCSV', [AccountController::class, 'storeCSV']);
Route::post('/accounts/store', [AccountController::class, 'store']);
Route::get('/accounts/{id}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
Route::put('/accounts/{id}', [AccountController::class, 'update'])->name('accounts.update');
Route::delete('/accounts/{id}/delete', [AccountController::class, 'destroy'])->name('accounts.destroy');
Route::get('/sites', [SiteController::class, 'index']);

Route::get('/downloadAccountCSVTemplate', [TemplateController::class, 'downloadAccountCSVTemplate']);