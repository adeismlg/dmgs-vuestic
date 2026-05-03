<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Langkah 1: Admin membuat pesanan (Single Gate)
     */
    public function createOrder(array $data): Order
    {
        $data['invoice_number'] = 'INV-' . strtoupper(Str::random(8));
        $data['status'] = Order::STATUS_PENDING_MEMBER;
        
        return Order::create($data);
    }

    /**
     * Langkah 2: Member menyetujui pesanan
     */
    public function acceptByMember(int $orderId): bool
    {
        $order = Order::findOrFail($orderId);
        // Keamanan: Hanya bisa accept jika status masih pending_member
        if ($order->status !== Order::STATUS_PENDING_MEMBER) return false;

        return $order->update(['status' => Order::STATUS_WAITING_PAYMENT]);
    }

    /**
     * Langkah 3: Admin memvalidasi pembayaran (Manual/Midtrans)
     */
    public function validatePayment(int $orderId): bool
    {
        $order = Order::findOrFail($orderId);
        if ($order->status !== Order::STATUS_WAITING_PAYMENT) return false;

        return $order->update(['status' => Order::STATUS_PROCESSING]);
    }

    /**
     * Langkah 4: Member menginput resi (Evidence)
     */
    public function shipOrder(int $orderId, string $receiptNumber): bool
    {
        $order = Order::findOrFail($orderId);
        if ($order->status !== Order::STATUS_PROCESSING) return false;

        return $order->update([
            'status' => Order::STATUS_SHIPPED,
            'shipping_receipt' => $receiptNumber
        ]);
    }

    /**
     * Langkah 5: Selesaikan Pesanan
     */
    public function completeOrder(int $orderId): bool
    {
        $order = Order::findOrFail($orderId);
        if ($order->status !== Order::STATUS_SHIPPED) return false;

        return $order->update(['status' => Order::STATUS_COMPLETED]);
    }
}