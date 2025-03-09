<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\SiteController;
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

Route::get('/accounts', [AccountController::class, 'index']);
Route::get('/accounts/create', [AccountController::class, 'create']);
Route::post('/accounts/store', [AccountController::class, 'store']);


Route::get('/sites', [SiteController::class, 'index']);