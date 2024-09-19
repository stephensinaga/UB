<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('register', 'register')->name('register');
    Route::post('register', 'registerSimpan')->name('register.simpan');
    Route::get('', 'login')->name('login');
    Route::post('login', 'loginAksi')->name('login.aksi');
    Route::get('logout', 'logout')->middleware('auth')->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'Dashboard'])->name('Dashboard');
        Route::get('create/product/view', [AdminController::class, 'CreateProductView'])->name('CreateProductView');
        Route::post('create/product', [AdminController::class, 'CreateProduct'])->name('CreateProductProcess');
        Route::delete('delete/product/{id}', [AdminController::class, 'DeleteProduct'])->name('DeleteProduct');
        Route::get('edit/product/view/{id}', [AdminController::class, 'EditProductView'])->name('EditProductView');
        Route::put('edit/product/{id}', [AdminController::class, 'EditProduct'])->name('EditProductProcess');

        Route::get('export/laporan/pdf', [AdminController::class, 'ExportLaporanPDF'])->name('ExportLaporanPDF');
        Route::get('laporan/view', [AdminController::class, 'SalesReport'])->name('SalesReportView');
    });
});

Route::prefix('cashier')->group(function () {
    Route::get('view', [CashierController::class, 'CashierView'])->name('CashierView');
    Route::post('order/selected/product/{id}', [CashierController::class, 'Order'])->name('OrderProduct');
    Route::post('checkout/pending/product', [CashierController::class, 'CheckOut'])->name('CheckOutProduct');
    Route::put('min/pending/order/{id}', [CashierController::class, 'MinOrderItem'])->name('MinOrderItem');

    // Route::put('checkout/pending/product', [CashierController::class, 'CheckOut'])->name('CheckOutProduct');
});