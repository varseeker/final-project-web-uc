<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\MenuController::class, 'index']);

Route::get('/media/menu/{v}/{src}', [App\Http\Controllers\MenuDisplayImageController::class, 'show'])
    ->where('src', '[A-Za-z0-9_-]+')
    ->name('media.menu.show');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\MenuController::class, 'orderPage'])->name('home');
    Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::delete('/home', [App\Http\Controllers\MenuController::class, 'destroy']);
    Route::put('/home', [App\Http\Controllers\MenuController::class, 'reduceItems']);
    Route::post('/home/store', [App\Http\Controllers\MenuController::class, 'store']);

    Route::post('/home/order', [App\Http\Controllers\HomeController::class, 'orderPage']);
    Route::post('/home/order/payment-success', [App\Http\Controllers\HomeController::class, 'paymentSuccess'])->name('payment-success');
    Route::post('/print', [App\Http\Controllers\HomeController::class, 'reciept'])->name('reciept');
});
