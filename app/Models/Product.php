<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'member_id', 
        'category_id', 
        'name', 
        'description', 
        'price', 
        'stock', 
        'image'
    ];

    public function member() { 
        return $this->belongsTo(Member::class); 
    }
    
    public function category() { 
        return $this->belongsTo(Category::class); 
    }

    // Helper untuk URL gambar yang profesional
    protected function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
