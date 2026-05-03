<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // 1. Validasi Signature (Keamanan: Memastikan ini benar-benar dari Midtrans)
        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Cari Order berdasarkan Invoice
        $order = Order::where('invoice_number', $request->order_id)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // 3. Update Status Berdasarkan Respon Midtrans
        $transactionStatus = $request->transaction_status;

        if ($transactionStatus == 'settlement') {
            // OTOMATISASI: Pembayaran berhasil, status jadi PROCESSING
            $order->update(['status' => Order::STATUS_PROCESSING]);
        } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {
            $order->update(['status' => Order::STATUS_CANCELED]);
        }

        return response()->json(['message' => 'Callback handled successfully']);
    }
}