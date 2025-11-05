<?php

use App\Http\Controllers\Payment\OrderController;
use App\Http\Controllers\Payment\TransferController;
use App\Http\Controllers\Payment\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment')->group(function () {
    Route::prefix('orders')->group(function () {
        Route::post('', [OrderController::class, 'create'])->name('api.payment.orders.create');
        Route::get('{id}', [OrderController::class, 'get'])->name('api.payment.orders.get');
        Route::post('{id}', [OrderController::class, 'cancel'])->name('api.payment.orders.cancel');
        Route::prefix('{id}/invoices')->group(function () {
            Route::get('', [OrderController::class, 'getInvoices'])->name('api.payment.orders.invoices.get');
            Route::get('{invoiceId}/download', [OrderController::class, 'downloadInvoice'])->name('api.payment.orders.invoices.download');
        });
    });

    Route::prefix('transfers')->group(function () {
        Route::get('account-balance', [TransferController::class, 'getBalance'])->name('api.payment.transfers.account-balance');
        Route::post('', [TransferController::class, 'create'])->name('api.payment.transfers.create');
        Route::post('batch', [TransferController::class, 'createBatch'])->name('api.payment.transfers.batch.create');
        Route::get('{id}', [TransferController::class, 'get'])->name('api.payment.transfers.get');
        Route::get('', [TransferController::class, 'list'])->name('api.payment.transfers.list');
        Route::post('estimate-credit', [TransferController::class, 'estimateCredit'])->name('api.payment.transfers.estimate-credit');
    });

    Route::prefix('webhooks')->group(function () {
        Route::post('verify', [WebhookController::class, 'verify'])->name('api.payment.webhooks.verify');
    });
});
