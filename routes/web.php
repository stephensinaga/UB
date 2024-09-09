<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('create/product/view', [AdminController::class, 'CreateProductView'])->name('CreateProductView');
    Route::post('create/product', [AdminController::class, 'CreateProduct'])->name('CreateProductProcess');
    Route::delete('delete/product/{id}', [AdminController::class, 'DeleteProduct'])->name('DeleteProduct');
    Route::get('edit/product/view/{id}', [AdminController::class, 'EditProductView'])->name('EditProductView');
    Route::put('edit/product/{id}', [AdminController::class, 'EditProduct'])->name('EditProductProcess');
});

Route::prefix('cashier')->group(function () {
    Route::get('view', [CashierController::class, 'CashierView'])->name('CashierView');
    Route::post('order/selected/product/{id}', [CashierController::class, 'Order'])->name('OrderProduct');
    Route::post('checkout/pending/product', [CashierController::class, 'CheckOut'])->name('CheckOutProduct');
    Route::delete('delete/pending/order/{id}', [CashierController::class, 'DeletePendingOrder'])->name('DeletePendingOrder');

    // Route::put('checkout/pending/product', [CashierController::class, 'CheckOut'])->name('CheckOutProduct');
});
