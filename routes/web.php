<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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



Route::middleware(['auth'])->group(function () {   
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/transaksiHistory', [DashboardController::class,'getHistoryByRekening'])->name('transaksiHistory');
    Route::get('/profile/{id}/edit', [DashboardController::class,'editCurrentUser'])->name('profile');
    Route::match(['put', 'patch'],'/profile/{id}', [DashboardController::class, 'updateCurrentUser'])->name('profile.edit-save');
    Route::post('/submitTransfer', [TransaksiController::class,'submitTransfer'])->name('transfer.submitTransfer');
    Route::post('/addRekening', [RekeningController::class,'addRekening'])->name('rekening.addRekening');
    Route::get('/cek-rekening/{id}', [RekeningController::class, 'cekRekening']);
    Route::resource('nasabah', NasabahController::class);
    Route::resource('produk', ProdukController::class);
    Route::resource('rekening', RekeningController::class);
    Route::resource('transaksi', TransaksiController::class);
});

Route::get('/', [MenuController::class, 'index']);
Route::get('/order', [MenuController::class, 'orderPage']);

Auth::routes();

Route::fallback(function () {
    return view('error-handling');
});

