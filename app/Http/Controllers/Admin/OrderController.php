<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'customer_name' => 'required|string',
            'customer_whatsapp' => 'required|string',
            'total_amount' => 'required|numeric',
            'shipping_cost' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $order = $this->orderService->createOrder($validated);
        return response()->json(['message' => 'Pesanan berhasil dibuat', 'data' => $order], 201);
    }

    public function confirmPayment(int $id): JsonResponse
    {
        $this->orderService->validatePayment($id);
        return response()->json(['message' => 'Pembayaran divalidasi, UMKM akan segera memproses.']);
    }
}