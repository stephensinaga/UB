<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExportLaporan;
use App\Http\Controllers\PoController;
use App\Http\Controllers\StockController;
use App\Models\Stock;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('register', 'register')->name('register');
        Route::post('register', 'registerSimpan')->name('register.simpan');
        Route::get('', 'login')->name('login');
        Route::post('login', 'loginAksi')->name('login.aksi');
        Route::get('logout', 'logout')->middleware('auth')->name('logout');
    });

    Route::prefix('guest')->group(function () {
        Route::post('save/session', [CashierController::class, 'SaveSession'])->name('SaveSession');
        Route::get('cashier/view', [CashierController::class, 'GuestView'])->name('GuestCashierView');
        Route::post('order/selected/product/{id}', [CashierController::class, 'GuestOrder'])->name('GuestOrder');
        Route::post('checkout/selected/order', [CashierController::class, 'GuestCheckout'])->name('GuestCheckout');
        // Route::put('min/pending/order/{id}', [CashierController::class, 'MinOrderItemGuest'])->name('MinOrderItemGuest');
        // Route::put('add/pending/order/{id}', [CashierController::class, 'AddOrderItemGuest'])->name('AddOrderItemGuest');
        Route::put('update/qty/order/{id}', [CashierController::class, 'UpdateOrderItemQtyGuest'])->name('UpdateOrderItemQtyGuest');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::get('logout', 'logout')->middleware('auth')->name('logout');
});

Route::middleware(['auth'])->group(function () {

    Route::get('dashboard', [AdminController::class, 'Dashboard'])->name('Dashboard');

    // Rute Admin dengan Middleware 'role:admin'
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('create/product/view', [AdminController::class, 'CreateProductView'])->name('CreateProductView');
        Route::post('create/product', [AdminController::class, 'CreateProduct'])->name('CreateProductProcess');
        Route::delete('delete/product/{id}', [AdminController::class, 'DeleteProduct'])->name('DeleteProduct');
        Route::get('edit/product/view/{id}', [AdminController::class, 'EditProductView'])->name('EditProductView');
        Route::put('edit/product/{id}', [AdminController::class, 'EditProduct'])->name('EditProductProcess');

        // Laporan & Export
        Route::get('export/laporan/pdf', [AdminController::class, 'ExportLaporanPDF'])->name('ExportLaporanPDF');
        Route::get('laporan/view', [AdminController::class, 'SalesReport'])->name('SalesReportView');
        Route::get('laporan/penjualan/all', [AdminController::class, 'laporanPenjualan'])->name('LaporanPenjualan');
        Route::get('export/laporan/penjualan/filtered', [ExportController::class, 'ExportLaporanPenjualan'])->name('ExportLaporanPenjualan');

        // WeeklyReceipts
        Route::prefix('weekly/receipts')->group(function () {
            Route::get('', [StockController::class, 'WeeklyReceiptsView'])->name('WeeklyReceiptsView');
            Route::post('add/pending/material', [StockController::class, 'InReceipts'])->name('InReceipts');
            Route::put('update/data/{id}', [StockController::class, 'UpdatePending'])->name('UpdatePending');
            Route::delete('delete/pending/{id}', [StockController::class, 'Deletepending'])->name('DeletePending');
            Route::post('save/material/into/stock', [StockController::class, 'SaveWeeklyReceipts'])->name('SaveWeeklyReceipts');
            Route::get('export/report', [StockController::class, 'ExportWeeklyReceipts'])->name('ExportWeeklyReceipts');
        });

        // Create new
        Route::post("create/new/material", [StockController::class, "CreateMaterial"])->name("CreateMaterial");
        Route::post("create/new/unit", [StockController::class, "CreateUnit"])->name("CreateUnit");

        // Pre Order
        Route::prefix("PO")->group(function () {
            Route::get("view", [PoController::class, 'view'])->name('PoBlade');
            Route::post('add/item', [PoController::class,'AddOrder'])->name('AddPoOrder');
            Route::delete('delete/item/{id}', [PoController::class,'Delete'])->name('DeletePoOrder');
            Route::post('proccess/order', [PoController::class, 'ProccessOrder'])->name('ProcessPoPendingOrder');

            Route::get('list', [PoController::class,'PoList'])->name('PoList');
            Route::delete('delete/po/{id}', [PoController::class,'DeletePO'])->name('DeletePO');
        });

        // Stock
        Route::prefix('stock')->group(function () {
            Route::get('material', [StockController::class, 'StorageView'])->name('StorageView');
            Route::post('add/new', [StockController::class, 'AddStock'])->name('AddStock');
            Route::get('material/update/view/{id}', [StockController::class, 'UpdateView'])->name('UpdateView');
            Route::post('update/material/stock/{id}', [StockController::class, 'UpdateProcess'])->name('UpdateProcess');
            Route::get('filter/material', [StockController::class, 'FilterMaterial'])->name('FilterMaterial');
            Route::get('export/report/material', [StockController::class, 'ExportLaporanStock'])->name('ExportLaporanStock');
        });
    });

    // Rute Cashier dengan Middleware 'role:cashier'
    Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->group(function () {
        Route::get('view', [CashierController::class, 'CashierView'])->name('CashierView');
        Route::post('order/selected/product/{id}', [CashierController::class, 'Order'])->name('OrderProduct');
        Route::post('checkout/pending/product', [CashierController::class, 'CheckOut'])->name('CheckOutProduct');
        Route::put('min/pending/order/{id}', [CashierController::class, 'MinOrderItem'])->name('MinOrderItem');
        Route::put('add/pending/order/{id}', [CashierController::class, 'AddOrderItem'])->name('AddOrderItem');

        Route::get('print/invoice/{id}', [CashierController::class, 'printInvoice'])->name('PrintInvoice');

        Route::get('list/pending/order', [CashierController::class, 'ListOrder'])->name('ListOrder');
        Route::put('process/pending/order/{id}/{type}/{cash}/{img}', [CashierController::class, 'ProcessPendingOrder'])->name('ProcessPendingOrder');
        Route::put('save/pending/order/{id}', [CashierController::class, 'SavePendingOrder'])->name('SavePendingOrder');
        Route::post('upload-transfer-proof', [CashierController::class, 'uploadTransferProof'])->name('uploadTransferProof');


        // Laporan Penjualan
        Route::get('history/penjualan', [AdminController::class, 'HistoryPenjualanCashier'])->name('HistoryPenjualanCashier');
        Route::get('detail/pembelian/customer/{id}', [AdminController::class, 'DetailLaporan'])->name('DetailLaporan');
        Route::get('export/laporan/penjualan/harian', [ExportController::class, 'ExportLaporanPenjualanHarian'])->name('ExportLaporanPenjualanHarian');
    });

});
