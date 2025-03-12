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
    return view('sites.index');
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
Route::get('/sites/create', [SiteController::class, 'create'])->name('site.create');
Route::post('/sites/store', [SiteController::class, 'store'])->name('sites.store');
Route::get('/sites/createCSV', [SiteController::class, 'createCSV'])->name('sites.createCSV');
Route::post('/sites/storeCSV', [SiteController::class, 'storeCSV'])->name('sites.storeCSV');
Route::delete('/sites/{id}/delete', [SiteController::class, 'delete'])->name('sites.delete');
Route::get('/sites/unupdated', [SiteController::class, 'showUnupdatedClients'])->name('sites.unUpdated');
Route::get('/downloadAccountCSVTemplate', [TemplateController::class, 'downloadAccountCSVTemplate']);
Route::get('/downloadSiteCSVTemplate', [TemplateController::class, 'downloadSitesCSVTemplate']);