<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\PaymentCallbackController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Member\DailySaleController;
use App\Http\Controllers\Member\OrderController as MemberOrderController;
use App\Http\Controllers\Member\ProductController as MemberProductController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('payment/callback', [PaymentCallbackController::class, 'handle']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Request $request) => $request->user());

    Route::prefix('admin')
        ->middleware('role:admin')
        ->group(function () {
            Route::post('orders', [AdminOrderController::class, 'store']);
            Route::patch('orders/{id}/confirm-payment', [AdminOrderController::class, 'confirmPayment']);
            Route::get('analytics', [AnalyticsController::class, 'adminDashboard']);
        });

    Route::prefix('member')
        ->middleware('role:member')
        ->group(function () {
            Route::post('products', [MemberProductController::class, 'store']);
            Route::patch('orders/{id}/accept', [MemberOrderController::class, 'accept']);
            Route::patch('orders/{id}/input-receipt', [MemberOrderController::class, 'inputReceipt']);
            Route::post('daily-sales', [DailySaleController::class, 'store']);
            Route::get('daily-sales/summary', [DailySaleController::class, 'index']);
            Route::get('analytics', [AnalyticsController::class, 'memberDashboard']);
        });

    Route::get('orders/{id}/payment-token', function (int $id, \App\Services\PaymentService $service) {
        $order = \App\Models\Order::findOrFail($id);

        return response()->json([
            'snap_token' => $service->getSnapToken($order),
        ]);
    });
});
