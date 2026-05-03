<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id', 
        'business_name', 
        'nib', 
        'phone', 
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
