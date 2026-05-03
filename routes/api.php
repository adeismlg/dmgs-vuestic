<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Member\ProductController as MemberProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Member\OrderController as MemberOrder;


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
        // Tambahkan put, delete, dll nanti
});
