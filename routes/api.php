<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Member\ProductController as MemberProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Jalur khusus Admin di sini
});

 Route::middleware('role:member')->prefix('member')->group(function () {
        Route::post('/products', [MemberProductController::class, 'store']);
        // Tambahkan put, delete, dll nanti
});
