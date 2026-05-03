<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentService
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION') === 'true';
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Membuat Snap Token untuk pembayaran di Flutter/Web
     */
    public function getSnapToken(Order $order): string
    {
        // Jika sudah ada token, kembalikan saja (mencegah duplikasi transaksi di Midtrans)
        if ($order->snap_token) {
            return $order->snap_token;
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->invoice_number,
                'gross_amount' => (int) ($order->total_amount + $order->shipping_cost),
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->customer_whatsapp,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);
        
        // Simpan token ke database
        $order->update(['snap_token' => $snapToken]);

        return $snapToken;
    }
}