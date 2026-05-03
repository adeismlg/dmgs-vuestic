<?php

namespace App\Http\Controllers\Member;

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

    public function accept(int $id): JsonResponse
    {
        $this->orderService->acceptByMember($id);
        return response()->json(['message' => 'Pesanan diterima, menunggu pembayaran pelanggan.']);
    }

    public function inputReceipt(Request $request, int $id): JsonResponse
    {
        $request->validate(['shipping_receipt' => 'required|string']);
        
        $this->orderService->shipOrder($id, $request->shipping_receipt);
        return response()->json(['message' => 'Nomor resi berhasil diinput.']);
    }
}