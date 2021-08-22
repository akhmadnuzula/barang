<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {return redirect()->route('login');})->name('root');

Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');




// API
// Route::get('/api/barang', [\App\Http\Controllers\BarangController::class, 'get'])->name('barang_get');
Route::get('/api/barang/{page}', [\App\Http\Controllers\BarangController::class, 'get'])->name('barang_get')->where('page', '[0-9]+');
Route::post('/api/barang', [\App\Http\Controllers\BarangController::class, 'create'])->name('barang_create');
Route::post('/api/barang/{id}', [\App\Http\Controllers\BarangController::class, 'update'])->name('barang_update')->where('id', '[0-9]+');
Route::get('/api/barang/delete/{id}', [\App\Http\Controllers\BarangController::class, 'delete'])->name('barang_del')->where('id', '[0-9]+');