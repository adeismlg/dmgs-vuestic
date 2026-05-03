<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    // Konstanta Status untuk keamanan logika
    const STATUS_PENDING_MEMBER = 'pending_member';   // Admin input, tunggu Member
    const STATUS_WAITING_PAYMENT = 'waiting_payment'; // Member OK, tunggu bayar ke Admin
    const STATUS_PROCESSING = 'processing';           // Admin validasi bayar, Member proses
    const STATUS_SHIPPED = 'shipped';                 // Member input resi
    const STATUS_COMPLETED = 'completed';             // Selesai
    const STATUS_CANCELED = 'canceled';               // Dibatalkan

    protected $fillable = [
        'invoice_number', 'member_id', 'customer_name', 
        'customer_whatsapp', 'total_amount', 'shipping_cost', 
        'shipping_receipt', 'status', 'notes'
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}