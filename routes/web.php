<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
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
    return view('checkout');
});

Route::get('/success.html', function () {
    return view('success');
});

Route::get('/cancel.html', function () {
    return view('cancel');
});

Route::post('/create-payment-link', [CheckoutController::class, 'createPaymentLink']);

Route::prefix('/order')->group(function () {
    Route::post('/create', [OrderController::class, 'createOrder']);
    Route::get('/{id}', [OrderController::class, 'getPaymentLinkInfoOfOrder']);
    Route::put('/{id}', [OrderController::class, 'cancelPaymentLinkOfOrder']);
});

Route::prefix('/payment')->group(function () { 
    Route::post('/payos', [PaymentController::class, 'handlePayOSWebhook']);
});

