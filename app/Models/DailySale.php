<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySale extends Model
{
    protected $fillable = ['member_id', 'sale_date', 'amount', 'notes'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}