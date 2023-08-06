<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'total_price',
        'status',
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
