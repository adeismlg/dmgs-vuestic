<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Member\ProductController as MemberProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Member\OrderController as MemberOrder;
use App\Http\Controllers\Member\DailySaleController;
use App\Http\Controllers\Api\PaymentCallbackController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::post('/orders', [AdminOrder::class, 'store']);
        Route::patch('/orders/{id}/confirm-payment', [AdminOrder::class, 'confirmPayment']);
    });

 Route::middleware('role:member')->prefix('member')->group(function () {
        Route::post('/products', [MemberProductController::class, 'store']);
        Route::patch('/orders/{id}/accept', [MemberOrder::class, 'accept']);
        Route::patch('/orders/{id}/input-receipt', [MemberOrder::class, 'inputReceipt']);
        Route::post('/daily-sales', [DailySaleController::class, 'store']);
        Route::get('/daily-sales/summary', [DailySaleController::class, 'index']);
        // Tambahkan put, delete, dll nanti
    });


// Endpoint untuk Midtrans (Tanpa Middleware Auth)
Route::post('/payment/callback', [PaymentCallbackController::class, 'handle']);

// Endpoint untuk mendapatkan token (Harus Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders/{id}/payment-token', function (int $id, \App\Services\PaymentService $service) {
        $order = \App\Models\Order::findOrFail($id);
        $token = $service->getSnapToken($order);
        return response()->json(['snap_token' => $token]);
    });
});
