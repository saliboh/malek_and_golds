<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GoldCalculatorController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes - Calculator and Admin
Route::middleware('auth')->group(function () {
    Route::get('/', [GoldCalculatorController::class, 'index']);
    Route::post('/calculate', [GoldCalculatorController::class, 'calculate']);
    Route::get('/admin', [GoldCalculatorController::class, 'admin']);
    Route::post('/admin/gold-prices', [GoldCalculatorController::class, 'storePrice']);

    // Receipt Buying Routes
    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::get('/', [ReceiptController::class, 'index'])->name('index');
        Route::get('/create', [ReceiptController::class, 'create'])->name('create');
        Route::post('/', [ReceiptController::class, 'store'])->name('store');
        Route::get('/{receipt}', [ReceiptController::class, 'show'])->name('show');
        Route::put('/{receipt}', [ReceiptController::class, 'update'])->name('update');
        Route::post('/{receipt}/offer', [ReceiptController::class, 'makeOffer'])->name('offer');
        Route::post('/{receipt}/accept', [ReceiptController::class, 'accept'])->name('accept');
        Route::post('/{receipt}/e-bagsak', [ReceiptController::class, 'markEBagsak'])->name('e-bagsak');
        Route::delete('/{receipt}', [ReceiptController::class, 'destroy'])->name('destroy');
    });
});

