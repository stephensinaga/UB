<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExportLaporan;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('register', 'register')->name('register');
        Route::post('register', 'registerSimpan')->name('register.simpan');
        Route::get('', 'login')->name('login');
        Route::post('login', 'loginAksi')->name('login.aksi');
        Route::get('logout', 'logout')->middleware('auth')->name('logout');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::get('logout', 'logout')->middleware('auth')->name('logout');
});



Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'Dashboard'])->name('Dashboard'); //Dashboard Admin - Cashier
        Route::get('create/product/view', [AdminController::class, 'CreateProductView'])->name('CreateProductView'); //Tampilan Penambahan product baru
        Route::post('create/product', [AdminController::class, 'CreateProduct'])->name('CreateProductProcess'); //Function Penambahan Product Baru
        Route::delete('delete/product/{id}', [AdminController::class, 'DeleteProduct'])->name('DeleteProduct'); //Function Delete Product
        Route::get('edit/product/view/{id}', [AdminController::class, 'EditProductView'])->name('EditProductView'); //Tampilan untuk Edit Product
        Route::put('edit/product/{id}', [AdminController::class, 'EditProduct'])->name('EditProductProcess'); //Function Update Product yg di edit

        // Bagian Laporan Pembelian Export PDF
        Route::get('export/laporan/pdf', [AdminController::class, 'ExportLaporanPDF'])->name('ExportLaporanPDF'); //Function Download Laporan PDF
        Route::get('laporan/view', [AdminController::class, 'SalesReport'])->name   ('SalesReportView'); //View Export Laporan PDF

        Route::get('laporan/penjualan/all', [AdminController::class, 'laporanPenjualan'])->name('LaporanPenjualan'); //Tampilan untuk Laporan Penjualan(Admin)

        Route::get('export/laporan/penjualan/filtered', [ExportController::class, 'ExportLaporanPenjualan'])->name('ExportLaporanPenjualan'); //Export Laporan Penjualan Filtered
    });
});

Route::prefix('cashier')->group(function () {
    Route::get('view', [CashierController::class, 'CashierView'])->name('CashierView'); //Tampilan Order Product
    Route::post('order/selected/product/{id}', [CashierController::class, 'Order'])->name('OrderProduct'); //Function Order Product
    Route::post('checkout/pending/product', [CashierController::class, 'CheckOut'])->name('CheckOutProduct'); //Function Checkout Product
    Route::put('min/pending/order/{id}', [CashierController::class, 'MinOrderItem'])->name('MinOrderItem'); //Function Mengurangi Qty
    Route::get('print/invoice/{id}', [CashierController::class, 'printInvoice'])->name('PrintInvoice'); //Function Test Print Invoice

    Route::get('history/penjualan/', [AdminController::class, 'HistoryPenjualanCashier'])->name('HistoryPenjualanCashier'); //View Laporan Penjualan per User
    Route::get('detail/pembelian/customer/{id}', [AdminController::class, 'DetailLaporan'])->name('DetailLaporan'); //Endpoint Detail Pembelian Tiap Customer
    Route::get('export/laporan/penjualan/harian', [ExportController::class, 'ExportLaporanPenjualanHarian'])->name('ExportLaporanPenjualanHarian'); //Export Laporan Penjualan Harian
Route::get('test/print', [CashierController::class, 'testPrinterConnection'])->name('testss');


    // Route::put('checkout/pending/product', [CashierController::class, 'CheckOut'])->name('CheckOutProduct');
});
